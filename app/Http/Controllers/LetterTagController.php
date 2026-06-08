<?php

namespace App\Http\Controllers;

use App\Models\LetterTag;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use App\Models\LetterTemplate;

class LetterTagController extends Controller
{
    public function index(Request $request)
    {
        $tags = LetterTag::orderBy('created_at', 'desc')->get();
        if ($request->ajax()) {
            $data = LetterTag::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btns = '<div class="btn-group btn-group-sm" role="group">';
                    $btns .=
                        '<button type="button" class="btn btn-outline-info btn-view"><i class="bi bi-eye"></i></button>';
                    $btns .=
                        '<button type="button" class="btn btn-outline-warning btn-edit"><i class="bi bi-pencil"></i></button>';
                    $btns .=
                        '<form action="' .
                        route('letter-tags.destroy', $row->id) .
                        '" method="POST" class="d-inline delete-form">
                                ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                              </form>';
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('tag_display', function ($row) {
                    return '<code>[' . $row->tag_name . ']</code>';
                })
                ->addColumn('input_type_badge', function ($row) {
                    // badge color based on input type
                    $class = match ($row->input_type) {
                        'long_text' => 'bg-primary',
                        'date' => 'bg-info text-dark',
                        'number' => 'bg-warning text-dark',
                        'time' => 'bg-danger',
                        'dropdown' => 'bg-success',
                        default => 'bg-secondary',
                    };

                    $badge =
                        '<span class="badge ' .
                        $class .
                        '">' .
                        ucfirst(str_replace('_', ' ', $row->input_type)) .
                        '</span>';

                    // additional marker if it has database / system configuration
                    if ($row->dropdown_type === 'model') {
                        $badge .=
                            '<br><small class="text-success fw-bold" style="font-size: 10px;"><i class="bi bi-database"></i> DB Relation</small>';
                    } elseif ($row->dropdown_type === 'auto_fill') {
                        $badge .=
                            '<br><small class="text-primary fw-bold" style="font-size: 10px;"><i class="bi bi-magic"></i> Auto System</small>';
                    }

                    return $badge;
                })
                ->editColumn('default_value', function ($row) {
                    // if it is an auto-fill system
                    if ($row->dropdown_type === 'auto_fill') {
                        $label = match ($row->dropdown_model) {
                            'user_name' => 'Employee Name (Creator)',
                            'user_position' => 'Position (Creator)',
                            'user_department' => 'Department (Creator)',
                            'today_date' => 'Today\'s Date',
                            default => 'System Data',
                        };
                        return '<span class="text-primary fw-bold fst-italic"><i class="bi bi-lightning-fill"></i> Auto: ' .
                            $label .
                            '</span>';
                    }

                    // if it is a database dropdown
                    if ($row->input_type === 'dropdown' && $row->dropdown_type === 'model') {
                        return '<span class="text-muted fst-italic">Fetch from Data: ' .
                            class_basename($row->dropdown_model) .
                            '</span>';
                    }

                    // if it is plain manual text
                    return $row->default_value ? Str::limit($row->default_value, 40) : '-';
                })
                ->addColumn('full_default_value', function ($row) {
                    return $row->default_value;
                })
                ->rawColumns(['action', 'input_type_badge', 'tag_display', 'default_value'])
                ->make(true);
        }

        return view('letter-tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        // open dropdown_type validation for 'auto_fill' as well
        $request->validate([
            'tag_name' => 'required|string|unique:letter_tags',
            'description' => 'nullable|string',
            'input_type' => 'required|string',
            'dropdown_type' => 'nullable|string|in:manual,model,auto_fill',
            'dropdown_options' => 'nullable|string',
            'dropdown_model' => 'nullable|string',
            'default_value' => 'nullable|string',
        ]);

        $data = $request->all();
        $data = $this->cleanTagData($data); // filter data to be neat when inserting to DB

        LetterTag::create($data);

        return redirect()->route('letter-tags.index')->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, LetterTag $letterTag)
    {
        $request->validate([
            'tag_name' => 'required|string|unique:letter_tags,tag_name,' . $letterTag->id,
            'description' => 'nullable|string',
            'input_type' => 'required|string',
            'dropdown_type' => 'nullable|string|in:manual,model,auto_fill',
            'dropdown_options' => 'nullable|string',
            'dropdown_model' => 'nullable|string',
            'default_value' => 'nullable|string',
        ]);

        $oldTagName = $letterTag->tag_name;
        $newTagName = $request->tag_name;

        $data = $request->all();
        $data = $this->cleanTagData($data); // filter data to be neat when inserting to DB

        $letterTag->update($data);

        // domino effect: update all templates if the tag name changes
        if ($oldTagName !== $newTagName) {
            $templates = LetterTemplate::where('content', 'like', '%[' . $oldTagName . ']%')->get();
            foreach ($templates as $template) {
                $newContent = str_replace('[' . $oldTagName . ']', '[' . $newTagName . ']', $template->content);
                $template->update(['content' => $newContent]);
            }
        }

        return redirect()->route('letter-tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(LetterTag $letterTag)
    {
        $letterTag->delete();
        return redirect()->route('letter-tags.index')->with('success', 'Tag deleted successfully.');
    }

    public function create()
    {
        return view('letter-tags.create');
    }

    public function edit(LetterTag $letterTag)
    {
        return view('letter-tags.edit', compact('letterTag'));
    }

    /**
     * core function: cleans up configuration relations so they do not overlap.
     * (example: if manual text is selected, dropdown/auto-fill options are discarded to null)
     */
    private function cleanTagData($data)
    {
        // if "Auto Fill" configuration is selected (e.g., for Text / Date input)
        if (isset($data['dropdown_type']) && $data['dropdown_type'] === 'auto_fill') {
            $data['dropdown_options'] = null; // discard manual options
            // keep the $data['dropdown_model'] value, as it is used to store 'user_name' or 'today_date'
        }
        // if "Dropdown" input is selected
        elseif (($data['input_type'] ?? '') === 'dropdown') {
            if (($data['dropdown_type'] ?? '') === 'manual') {
                $data['dropdown_model'] = null;
            } else {
                $data['dropdown_options'] = null;
            }
        }
        // if plain text input without any configuration
        else {
            $data['dropdown_type'] = null;
            $data['dropdown_options'] = null;
            $data['dropdown_model'] = null;
        }

        return $data;
    }
}
