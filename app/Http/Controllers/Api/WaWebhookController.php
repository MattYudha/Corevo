<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WaWebhookController extends Controller
{
    public function handleCheckout(Request $request)
    {
        try {
            $phone = $request->phone;
            $lat = $request->latitude;
            $lng = $request->longitude;

            // 1. Bersihkan semua karakter selain angka
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // 2. Ambil "nomor inti" (buang 62 atau 0 di depan)
            $coreNumber = $phone;
            if (str_starts_with($phone, '62')) {
                $coreNumber = substr($phone, 2); // potong 2 angka depan
            } elseif (str_starts_with($phone, '0')) {
                $coreNumber = substr($phone, 1); // potong 1 angka depan
            }

            // 3. Cari karyawan pakai nomor inti (pasti dapet mau dia pake 08 atau 628)
            $employee = Employee::where('phone_number', 'like', "%{$coreNumber}%")->first();

            if (!$employee) {
                // Biar lu gampang debugging, kita tampilin nomor intinya di error
                return response()->json(['status' => false, 'message' => "❌ nomor hp kamu belum terdaftar di sistem hr kami. (debug: mencari nomor {$coreNumber})"]);
            }

            // check if they have check-in today
            $presence = Presence::where('employee_id', $employee->id)
                ->whereDate('date', Carbon::today())
                ->first();

            if (!$presence) {
                return response()->json(['status' => false, 'message' => '⚠️ kamu belum melakukan check-in hari ini. silahkan check-in dulu dari aplikasi ya!']);
            }

            if ($presence->check_out) {
                return response()->json(['status' => false, 'message' => '✅ kamu sudah melakukan check-out hari ini. selamat beristirahat!']);
            }

            // // do the check-out process
            // $presence->update([
            //     'check_out' => Carbon::now()->format('H:i:s'),
            //     'latitude_out' => $lat, 
            //     'longitude_out' => $lng 
            // ]);

            // Kunci zona waktu ke WIB (Jakarta) biar nggak meleset
            // $waktuSekarang = Carbon::now('Asia/Jakarta');

            // // do the check-out process
            // $presence->check_out = $waktuSekarang->format('H:i:s');
            // $presence->check_out_latitude = $lat; 
            // $presence->check_out_longitude = $lng; 
            // $presence->save(); // Simpan paksa tanpa peduli aturan $fillable

            // $time = $waktuSekarang->format('H:i');
            // return response()->json(['status' => true, 'message' => "✅ mantap bosku! berhasil check-out pada pukul {$time} wib."]);

            $waktuSekarang = Carbon::now('Asia/Jakarta');

            // update format to full datetime (Y-m-d H:i:s)
            $presence->check_out = $waktuSekarang->format('Y-m-d H:i:s');
            $presence->check_out_latitude = $lat; 
            $presence->check_out_longitude = $lng; 
            $presence->save();

            // for wa message, keep using only hours and minutes
            $time = $waktuSekarang->format('H:i');
            return response()->json(['status' => true, 'message' => "✅ mantap bosku! berhasil check-out pada pukul {$time} wib."]);

            // $time = Carbon::now()->format('H:i');
            // return response()->json(['status' => true, 'message' => "✅ mantap bosku! berhasil check-out pada pukul {$time} wib."]);

        } catch (\Exception $e) {
            Log::error('webhook wa error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => '❌ terjadi kesalahan pada server hr.']);
        }
    }
}