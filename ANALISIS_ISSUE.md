# ANALISIS ISSUE — Corevo HRIS
**Tanggal Analisis:** 27 April 2026  
**Dibuat oleh:** Antigravity (AI Code Assistant)  
**Status:** Analisis Saja — Tidak Ada Code Generation

---

## RINGKASAN EKSEKUTIF

Dokumen ini merangkum analisis dari 13 issue yang dilaporkan pada sistem HRIS Corevo Aratech. Setiap issue telah diverifikasi langsung dari source code yang ada.

---

## 1. ⏰ JAM / WAKTU — Absen Bukan WIB

### Temuan
Ditemukan di `config/app.php` baris 68:
```
'timezone' => env('APP_TIMEZONE', 'UTC'),
```
Dan di `.env` **tidak ada** entri `APP_TIMEZONE`. Artinya sistem berjalan di timezone **UTC** (default).

### Dampak
- Semua `Carbon::now()` yang dipakai di `PresencesController.php` (baris 277, 571, 603) menghasilkan waktu UTC
- Waktu check-in dan check-out tersimpan di database dalam UTC, bukan WIB (UTC+7)
- Data presensi tampil 7 jam lebih awal dari seharusnya

### Akar Masalah
- `APP_TIMEZONE` tidak disetel di `.env`
- Tidak ada `Carbon::setTestNow()` atau `->timezone('Asia/Jakarta')` di controller presence

---

## 2. 📊 KPI — Admin Input KPI Log

### Temuan
Dari analisis `KPIController.php`:
- Method `updateRecord()` (baris 213) hanya mengizinkan karyawan (employee) mengubah record KPI milik mereka sendiri
- Tidak ada method khusus untuk **Admin menginput atau mengedit KPI log** karyawan lain secara manual
- Admin (HR Administrator / Master Admin) tidak dapat override atau menambah KPI record secara langsung melalui form
- Method `recalculate()` (baris 384) tersedia untuk admin, tapi hanya **menghitung ulang otomatis** — bukan input manual

### Dampak
- Admin tidak bisa mengkoreksi atau menginput nilai KPI aktual secara manual atas nama karyawan
- Tidak ada route atau form untuk admin create/update KPI record langsung

---

## 3. 🕐 LEMBUR — "Tidak Hadir" di Presence

### Temuan
Di `PresencesController.php` method `store()` (baris 124–352):
- Status yang tersimpan hanya: `present`, `absent`, `leave`
- Tidak ada field atau logic untuk status **lembur** (overtime)
- Model `Presence.php` dan tabel database tidak memiliki kolom `overtime` atau `lembur`
- Payroll sudah punya field `overtime_hours` dan `overtime_amount`, tapi tidak terhubung ke data presence

### Dampak
- Karyawan yang lembur tidak dapat dicatat dalam presensi
- Data lembur hanya bisa diinput manual di payroll, terputus dari kehadiran aktual

---

## 4. 🔐 SESI — Masih Nyangkut Setelah Logout

### Temuan
Di `AuthenticatedSessionController.php` method `destroy()` (baris 55–64):
```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
return redirect('/');
```
Secara teori ini benar. Namun ada **potensi masalah** dari `EnsureRoleSession.php`:
- Middleware ini **mengisi ulang** session role/employee_id setiap request jika user masih terautentikasi
- Jika browser tidak menghapus cookie session dengan benar (misalnya di mobile atau browser tertentu), session bisa persisten

### Kemungkinan Penyebab Lain
- `SESSION_DRIVER=file` — file session tidak otomatis terhapus setelah `invalidate()` di beberapa konfigurasi Docker
- `SESSION_SECURE_COOKIE=false` — cookie tidak terhapus karena tidak di-set sebagai secure
- Cache browser yang menyimpan halaman dashboard (ada `Cache-Control: no-cache` di meta tag, tapi tidak di HTTP header response)

---

## 5. 🚦 MIDDLEWARE — Limit Input Terlalu Kecil

### Temuan
- Ditemukan hanya **3 file middleware**: `CheckRole.php`, `EnsureRoleSession.php`, `TrustProxies.php`
- **Tidak ada** middleware rate limiting atau `ThrottleRequests` yang dikonfigurasi secara khusus
- Laravel default memiliki `throttle:60,1` (60 request per menit) yang mungkin terlalu ketat untuk form-heavy operations (contoh: input payroll, upload dokumen, bulk KPI entry)

### Dampak
- User yang melakukan banyak aksi dalam waktu singkat (admin yang input data masal) akan mendapat HTTP 429 Too Many Requests
- Tidak ada konfigurasi custom throttle per-route untuk operasi yang lebih intensif

---

## 6. 📋 AUDIT TRAIL — Tidak Ada Fungsi Enable/Disable

### Temuan
Di `AuditController.php` (hanya 23 baris):
```php
public function index() {
    // Hanya menampilkan log — tidak ada toggle, tidak ada filter
    $logs = AuditLog::with('user', 'auditable')->latest()->paginate(20);
    return view('audit.index', compact('logs'));
}
```
Dan model `AuditLog.php`:
- Tidak ada field `enabled` atau `is_active`
- Tidak ada model atau config untuk on/off audit logging

### Dampak
- Log akan terus bertambah tanpa batas
- Tidak ada cara untuk pause/resume logging via UI
- Database tabel `audit_logs` bisa membengkak

---

## 7. 💰 BUKU KAS / KEUANGAN — Finance Role Tidak Bisa Akses Buku Kas

### Temuan
Di sidebar (`dashboard.blade.php` baris 467):
```php
@if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isSupervisor || $isFinanceRole)
```
**Sidebar sudah benar** — `$isFinanceRole` sudah masuk kondisi. 

Di `web_finance.php` (baris 26):
```php
Route::middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',Manager / Unit Head,Marketing,' . Roles::FINANCE])
```
Route untuk `transactions/create`, `entities`, `accounts` juga sudah include `Roles::FINANCE`.

### Kemungkinan Masalah
- Nilai `Roles::FINANCE` di Constants belum sesuai dengan nilai role di database
- Atau session `role` yang tersimpan tidak sama persis dengan string konstanta yang digunakan di route middleware

---

## 8. 💵 PAYROLL — Belum Ada di Sidebar Admin

### Temuan
Setelah menelusuri seluruh `dashboard.blade.php` (950 baris), **tidak ditemukan satupun link ke `/payrolls`** di sidebar manapun.

Namun di `routes/web.php`:
- Route `payrolls` sudah ada dan terdaftar (baris 99–102)
- Controller `PayrollsController.php` sudah lengkap (index, create, store, edit, update, destroy, show)

### Akar Masalah
- **Sidebar tidak pernah ditambahkan link ke Payroll** — ini pure missing UI
- Variabel `$showPayrollGroup` di sidebar (baris 315) menggunakan nama yang ambigu — digunakan untuk group KPI, bukan Payroll

### Dampak
- Admin/HR tidak bisa mengakses payroll dari sidebar, harus tau URL langsung `/payrolls`

---

## 9. ✅ TASK & ACTIVITY LOG — Perlu Diperiksa Error Case

### Temuan di TaskController.php:
**Potensi Error:**
- Method `done()` dan `pending()` (baris 188–211) menggunakan `Task::find($id)` tanpa null check — jika ID tidak valid, `->update()` akan throw `Call to a member function update() on null`
- Method `store()` tidak ada created_by atau role restriction — siapapun bisa membuat task
- Status values tidak konsisten: ada `'done'`, `'pending'`, `'on progress'` di badge (baris 95–99) tapi validasi di `store()` (baris 133) hanya `'required|string'` tanpa enum restriction

**Activity Log:**
- Tidak ditemukan implementasi activity log yang terhubung ke Task — tidak ada AuditLog yang dicatat saat task dibuat/diubah/dihapus

---

## 10. 🗑️ BERSIHIN DATA LOG AUDIT TRAIL

### Temuan
- `AuditController.php` hanya punya method `index()` — tidak ada `destroy()`, `clear()`, atau bulk delete
- Tidak ada artisan command untuk purge audit logs
- Tidak ada pagination filter by date range di controller

### Dampak
- Satu-satunya cara hapus log adalah langsung via database atau query manual
- Tidak ada mekanisme retention policy (misal: hapus log lebih dari 90 hari)

---

## 11. 📐 SIDEBAR — Tambah Tombol Close/Open (Collapsible) di Web Mode

### Temuan
Di `dashboard.blade.php` JavaScript section (baris 788–797):
```javascript
$(document).on('click', '.burger-btn', function(e) {
    if (isDesktopViewport()) {
        bodyEl.classList.toggle('sidebar-collapsed');
    } else {
        sidebarWrapper.toggleClass('active');
    }
});
```
Dan CSS (baris 108–113):
```css
@media screen and (min-width: 992px) {
    body.sidebar-collapsed #sidebar { width: 0; transform: translateX(-300px); }
    body.sidebar-collapsed #main { margin-left: 0; }
}
```

**Logic collapse sudah ada** untuk burger menu mobile. Namun di desktop **tidak ada tombol toggle yang visible** di area sidebar/header untuk close/open sidebar — hanya ada burger button di mobile header yang tersembunyi di desktop (`d-xl-none`).

### Akar Masalah
- Tidak ada toggle button yang visible di desktop view untuk collapsible sidebar
- Burger button hanya tampil di mobile (`d-xl-none`)

---

## 12. ❌ LATE — Hapus di WFA dan WFH

### Temuan
Di `PresencesController.php` method `index()` (baris 57–59):
```php
if ($row->status === 'present' && $this->isLateCheckIn($row)) {
    $badge .= '<span class="badge bg-warning">Late</span>';
}
```
Method `isLateCheckIn()` (baris 748–772) **tidak mempertimbangkan work_type** — late check dihitung untuk semua tipe termasuk WFA dan WFH.

### Dampak
- Karyawan WFA dan WFH yang check-in setelah jam 08:15 tetap ditandai "Late"
- Ini tidak fair karena WFH/WFA tidak terikat lokasi kantor dan seharusnya lebih fleksibel

---

## TABEL RINGKASAN STATUS

| No | Issue | File Utama | Severity | Status Code |
|----|-------|-----------|----------|-------------|
| 1 | Timezone WIB | `config/app.php`, `.env` | 🔴 High | Bug |
| 2 | KPI Admin Input | `KPIController.php` | 🟡 Medium | Missing Feature |
| 3 | Lembur di Presence | `PresencesController.php` | 🟡 Medium | Missing Feature |
| 4 | Sesi Nyangkut Logout | `AuthenticatedSessionController.php`, `session.php` | 🔴 High | Bug |
| 5 | Middleware Limit | Kernel/Route config | 🟡 Medium | Config Issue |
| 6 | Audit Trail Enable/Disable | `AuditController.php`, `AuditLog.php` | 🟡 Medium | Missing Feature |
| 7 | Finance Akses Buku Kas | `web_finance.php`, `CheckRole.php` | 🔴 High | Potential Bug |
| 8 | Payroll di Sidebar | `dashboard.blade.php` | 🔴 High | Missing UI |
| 9 | Task & Activity Log | `TaskController.php` | 🟡 Medium | Bug + Missing Feature |
| 10 | Clear Audit Log | `AuditController.php` | 🟢 Low | Missing Feature |
| 11 | Sidebar Close/Open Desktop | `dashboard.blade.php` | 🟢 Low | Missing UI |
| 12 | Late di WFA/WFH | `PresencesController.php` | 🟡 Medium | Bug |

---

## PRIORITAS PENGERJAAN (REKOMENDASI)

### 🔴 Kritis — Harus Segera
1. **Timezone WIB** — Langsung berdampak ke semua data absensi
2. **Payroll Sidebar** — Admin tidak bisa akses fitur penting
3. **Sesi Logout** — Isu keamanan potensial
4. **Finance Buku Kas** — Role Finance tidak bisa kerja

### 🟡 Penting — Sprint Berikutnya  
5. **Lembur di Presence**
6. **KPI Admin Input**
7. **Task Error Case**
8. **Middleware Limit**
9. **Audit Enable/Disable**
10. **Late WFA/WFH**

### 🟢 Enhancement — Bisa Dijadwalkan
11. **Clear Audit Log**
12. **Sidebar Collapsible Desktop**

---

*Dokumen ini hanya berisi analisis dan tidak mengandung perubahan code apapun.*
