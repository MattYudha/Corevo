<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = WorkLog::with(['employee', 'task'])
                            ->select('work_logs.*')
                            ->latest('work_logs.log_date');


            if (Auth::user()->isMasterAdmin()) {
                if ($request->has('employee_filter') && $request->employee_filter != '') {
                    $query->where('work_logs.employee_id', $request->employee_filter);
                }
            } else {
                $query->where('work_logs.employee_id', Auth::user()->employee_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('log_date', fn($row) => Carbon::parse($row->log_date)->format('d M Y'))
                ->addColumn('employee_name', fn($row) => $row->employee ? $row->employee->fullname : 'Tidak Diketahui')
                ->editColumn('description', fn($row) => Str::limit($row->description, 50, '...'))
                ->addColumn('task_info', function($row) {
                    if ($row->task) {
                        return '<a href="'.route('tasks.show', $row->task->id).'" class="badge bg-info text-decoration-none">'.($row->task->title).'</a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function($row) {
                    $isOwner = Auth::user()->employee_id == $row->employee_id;
                    $isAdmin = Auth::user()->isMasterAdmin();
                    
                    $btn = '<div class="d-flex gap-1">';
                    $btn .= '<a href="'.route('work-logs.show', $row->id).'" class="btn btn-sm btn-outline-info" title="Lihat Detail"><i class="bi bi-eye"></i></a>';
                    
                    if ($isOwner || $isAdmin) {
                        $btn .= '<a href="'.route('work-logs.edit', $row->id).'" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>';
                        $btn .= '<form action="'.route('work-logs.destroy', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus log aktivitas ini?\')">
                                    '.csrf_field().method_field('DELETE').'
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['task_info', 'action'])
                ->make(true);
        }

        $employees = [];
        if (Auth::user()->isMasterAdmin()) {
            $employees = Employee::orderBy('fullname', 'asc')->get();
        }

        return view('work-logs.index', compact('employees'));
    }

    public function create()
    {
        $myEmployeeId = Auth::user()->employee_id;
        
        // fetch employee tasks that are still pending (status is not 'completed')
        $tasks = Task::where('assigned_to', $myEmployeeId)
                     ->where('status', '!=', 'done')
                     ->orderBy('title', 'asc')
                     ->get();

        return view('work-logs.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'log_date' => 'required|date',
            'description' => 'required|string',
            'task_id' => 'nullable|exists:tasks,id',
            'evidence' => 'nullable|file|max:5120',
        ]);

        $myEmployeeId = Auth::user()->employee_id;
        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('work_logs', 'public');
        }

        $taskCommentId = null;

        // if this is related to a task, create its comment first
        if ($request->task_id) {
            $taskComment = TaskComment::create([
                'task_id' => $request->task_id,
                'employee_id' => $myEmployeeId,
                'comment' => "[Log Aktivitas " . date('d/m/Y', strtotime($request->log_date)) . "]: " . $request->description,
                'evidence_path' => $evidencePath,
            ]);
            
            // save the comment id
            $taskCommentId = $taskComment->id;
            
            $task = Task::find($request->task_id);
            if ($task && $task->status == 'pending') {
                $task->update(['status' => 'in_progress']);
            }
        }

        // store the activity log with the new comment id
        WorkLog::create([
            'employee_id' => $myEmployeeId,
            'task_id' => $request->task_id,
            'task_comment_id' => $taskCommentId,
            'log_date' => $request->log_date,
            'description' => $request->description,
            'evidence_path' => $evidencePath,
        ]);

        return redirect()->route('work-logs.index')->with('success', 'Log aktivitas berhasil disimpan.');
    }

    public function show($id) 
    {
        $work_log = WorkLog::findOrFail($id);

        if (!Auth::user()->isMasterAdmin() && Auth::user()->employee_id != $work_log->employee_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat log aktivitas ini.');
        }

        return view('work-logs.show', compact('work_log'));
    }

    public function edit($id)
    {
        $work_log = WorkLog::findOrFail($id);

        if (!Auth::user()->isMasterAdmin() && Auth::user()->employee_id != $work_log->employee_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah log aktivitas ini.');
        }

        $myEmployeeId = Auth::user()->employee_id;
        
        if (Auth::user()->isMasterAdmin()) {
            $tasks = Task::where('status', '!=', 'done')
                         ->orWhere('id', $work_log->task_id)
                         ->orderBy('title', 'asc')
                         ->get();
        } else {
            $tasks = Task::where('assigned_to', $myEmployeeId)
                         ->where(function($query) use ($work_log) {
                             $query->where('status', '!=', 'done')
                                   ->orWhere('id', $work_log->task_id);
                         })
                         ->orderBy('title', 'asc')
                         ->get();
        }

        return view('work-logs.edit', compact('work_log', 'tasks'));
    }

    public function update(Request $request, $id)
    {
        $work_log = WorkLog::findOrFail($id);

        if (!Auth::user()->isMasterAdmin() && Auth::user()->employee_id != $work_log->employee_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah log aktivitas ini.');
        }

        $request->validate([
            'log_date' => 'required|date',
            'description' => 'required|string',
            'task_id' => 'nullable|exists:tasks,id',
            'evidence' => 'nullable|file|max:5120',
        ]);

        $path = $work_log->evidence_path;
        if ($request->hasFile('evidence')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('evidence')->store('work_logs', 'public');
        }

        $taskCommentId = $work_log->task_comment_id;

        // synchronize with related task comments
        if ($request->task_id) {
            $teksKomentar = "[Log Aktivitas " . date('d/m/Y', strtotime($request->log_date)) . "]: " . $request->description;
            
            if ($taskCommentId) {
                // update existing comment text and attachments if present
                $taskComment = TaskComment::find($taskCommentId);
                if ($taskComment) {
                    $taskComment->update([
                        'task_id' => $request->task_id,
                        'comment' => $teksKomentar,
                        'evidence_path' => $path,
                    ]);
                }
            } else {
                // handle case where a task link is added after previously being absent
                $komentarBaru = TaskComment::create([
                    'task_id' => $request->task_id,
                    'employee_id' => $work_log->employee_id,
                    'comment' => $teksKomentar,
                    'evidence_path' => $path,
                ]);
                $taskCommentId = $komentarBaru->id;
            }
        } else {
            // handle case where the related task link is removed
            if ($taskCommentId) {
                TaskComment::destroy($taskCommentId);
                $taskCommentId = null;
            }
        }

        $work_log->update([
            'log_date' => $request->log_date,
            'description' => $request->description,
            'task_id' => $request->task_id,
            'task_comment_id' => $taskCommentId,
            'evidence_path' => $path,
        ]);

        return redirect()->route('work-logs.index')->with('success', 'Log aktivitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $work_log = WorkLog::findOrFail($id);

        if (!Auth::user()->isMasterAdmin() && Auth::user()->employee_id != $work_log->employee_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus log aktivitas ini.');
        }

        // remove the linked task comment first if present
        if ($work_log->task_comment_id) {
            TaskComment::destroy($work_log->task_comment_id);
        }

        if ($work_log->evidence_path) {
            Storage::disk('public')->delete($work_log->evidence_path);
        }
        
        $work_log->delete();

        return redirect()->route('work-logs.index')->with('success', 'Log aktivitas berhasil dihapus.');
    }
}