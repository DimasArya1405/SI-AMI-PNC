<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AkunDosenController;
use App\Http\Controllers\Admin\Akun\AuditeeController;
use App\Http\Controllers\Auditee\AuditeeController as RoleAuditeeController;
use App\Http\Controllers\Admin\Akun\AuditorController as AdminAkunAuditorController;
use App\Http\Controllers\Admin\Akun\DosenController;
use App\Http\Controllers\Admin\Akun\KepalaP4mpController;
use App\Http\Controllers\Admin\Ami\ItemSubStandarMutuController;
use App\Http\Controllers\Admin\Ami\StandarMutuController;
use App\Http\Controllers\Admin\Ami\SubStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptItemSubStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptSubStandarMutuController;
use App\Http\Controllers\Admin\Data\ProdiController;
use App\Http\Controllers\Admin\MonitoringTindakanKoreksiController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\Data\UPTController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Admin\RkaController as AdminRkaController;
use App\Http\Controllers\Auditee\DataDosenController;
use App\Http\Controllers\Auditee\RkaController;
use App\Http\Controllers\Auditee\StandarAMIController;
use App\Http\Controllers\Auditee\TindakanKoreksiController as AuditeeTindakanKoreksiController;
use App\Http\Controllers\Auditor\AuditorController;
use App\Http\Controllers\Auditor\PenugasanController as AuditorPenugasanController;
use App\Http\Controllers\Auditee\PenugasanController as AuditeePenugasanController;
use App\Http\Controllers\Auditor\PelaksanaanAuditController;
use App\Http\Controllers\Auditor\RkaController as AuditorRkaController;
use App\Http\Controllers\Auditor\TindakanKoreksiController as AuditorTindakanKoreksiController;
use App\Http\Controllers\Dosen\DosenController as RoleDosenController;
use App\Http\Controllers\KepalaP4mp\DashboardController as KepalaP4mpDashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route umum setelah login. Setiap role tetap diarahkan ke dashboard masing-masing.
Route::get('/dashboard', function () {
    $role = request()->user()?->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'kepala_p4mp' => redirect()->route('kepala_p4mp.dashboard'),
        'auditor' => redirect()->route('auditor.dashboard'),
        'auditee' => redirect()->route('auditee.dashboard'),
        default => redirect()->route('dosen.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Grouping berdasarkan role
Route::middleware(['auth', 'checkRole:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/akun/auditor', [AdminAkunAuditorController::class, 'index'])->name('admin.akun.auditor');
    Route::post('/admin/akun/auditor/tambah', [AdminAkunAuditorController::class, 'tambah'])->name('admin.auditor.tambah');
    Route::put('/admin/akun/auditor/edit', [AdminAkunAuditorController::class, 'edit'])->name('admin.auditor.edit');
    Route::delete('/admin/akun/auditor/hapus', [AdminAkunAuditorController::class, 'hapus'])->name('admin.auditor.hapus');
    Route::get('/admin/akun/auditee', [AuditeeController::class, 'index'])->name('admin.akun.auditee');
    Route::post('/admin/akun/auditee/tambah', [AuditeeController::class, 'tambah'])->name('admin.auditee.tambah');
    Route::put('/admin/akun/auditee/edit', [AuditeeController::class, 'edit'])->name('admin.auditee.edit');
    Route::delete('/admin/akun/auditee/hapus', [AuditeeController::class, 'hapus'])->name('admin.auditee.hapus');
    Route::put('/admin/akun/auditee/aktivasi', [AuditeeController::class, 'aktivasi'])->name('admin.auditee.aktivasi');
    Route::get('/admin/akun/kepala-p4mp', [KepalaP4mpController::class, 'index'])->name('admin.akun.kepala_p4mp');
    Route::post('/admin/akun/kepala-p4mp/tambah', [KepalaP4mpController::class, 'tambah'])->name('admin.kepala_p4mp.tambah');
    Route::put('/admin/akun/kepala-p4mp/edit', [KepalaP4mpController::class, 'edit'])->name('admin.kepala_p4mp.edit');
    Route::delete('/admin/akun/kepala-p4mp/hapus', [KepalaP4mpController::class, 'hapus'])->name('admin.kepala_p4mp.hapus');
    Route::get('/admin/akun/dosen', [DosenController::class, 'index'])->name('admin.akun.dosen');
    Route::post('/admin/akun/dosen/tambah', [DosenController::class, 'tambah'])->name('admin.dosen.tambah');
    Route::put('/admin/akun/dosen/edit', [DosenController::class, 'edit'])->name('admin.dosen.edit');
    Route::delete('/admin/akun/dosen/hapus', [DosenController::class, 'hapus'])->name('admin.dosen.hapus');

    Route::get('/admin/data/prodi', [ProdiController::class, 'index'])->name('admin.data.prodi');
    Route::post('/admin/data/prodi/tambah', [ProdiController::class, 'tambah'])->name('admin.prodi.tambah');
    Route::put('/admin/data/prodi/edit', [ProdiController::class, 'edit'])->name('admin.prodi.edit');
    Route::delete('/admin/data/prodi/hapus', [ProdiController::class, 'hapus'])->name('admin.prodi.hapus');

    // ROUTE PERIODE
    Route::get('/admin/periode', [PeriodeController::class, 'index'])->name('admin.periode');
    Route::post('/admin/periode/tambah', [PeriodeController::class, 'tambah'])->name('admin.periode.tambah');
    Route::delete('/admin/periode/hapus', [PeriodeController::class, 'hapus'])->name('admin.periode.hapus');

    Route::get('/admin/data/upt', [UPTController::class, 'index'])->name('admin.data.upt');
    Route::post('/admin/data/upt/tambah', [UPTController::class, 'tambah'])->name('admin.upt.tambah');
    Route::put('/admin/data/upt/edit', [UPTController::class, 'edit'])->name('admin.upt.edit');
    Route::delete('/admin/data/upt/hapus', [UPTController::class, 'hapus'])->name('admin.upt.hapus');

    Route::get('/admin/ami/standar-mutu', [StandarMutuController::class, 'index'])->name('admin.ami.standar_mutu');
    Route::post('/admin/ami/standar-mutu/tambah', [StandarMutuController::class, 'tambah'])->name('admin.standar_mutu.tambah');
    Route::put('/admin/ami/standar-mutu/edit', [StandarMutuController::class, 'edit'])->name('admin.standar_mutu.edit');
    Route::delete('/admin/ami/standar-mutu/hapus', [StandarMutuController::class, 'hapus'])->name('admin.standar_mutu.hapus');

    Route::get('/admin/ami/standar-mutu/sub-standar-mutu/{standar_mutu_id}', [SubStandarMutuController::class, 'index'])->name('admin.ami.sub_standar_mutu');
    Route::post('/admin/ami/standar-mutu/sub-standar-mutu/tambah', [SubStandarMutuController::class, 'tambah'])->name('admin.sub_standar_mutu.tambah');
    Route::put('/admin/ami/standar-mutu/sub-standar-mutu/edit', [SubStandarMutuController::class, 'edit'])->name('admin.sub_standar_mutu.edit');
    Route::delete('/admin/ami/standar-mutu/sub-standar-mutu/hapus', [SubStandarMutuController::class, 'hapus'])->name('admin.sub_standar_mutu.hapus');

    Route::get('/admin/ami/standar-mutu/sub-standar-mutu/item/{sub_standar_id}', [ItemSubStandarMutuController::class, 'index'])->name('admin.ami.item_sub_standar_mutu');
    Route::post('/admin/ami/standar-mutu/sub-standar-mutu/item/tambah', [ItemSubStandarMutuController::class, 'tambah'])->name('admin.item_sub_standar_mutu.tambah');
    Route::put('/admin/ami/standar-mutu/sub-standar-mutu/item/edit', [ItemSubStandarMutuController::class, 'edit'])->name('admin.item_sub_standar_mutu.edit');
    Route::delete('/admin/ami/standar-mutu/sub-standar-mutu/item/hapus', [ItemSubStandarMutuController::class, 'hapus'])->name('admin.item_sub_standar_mutu.hapus');

    Route::get('/admin/ami/pemetaan-standar-mutu', [UptStandarMutuController::class, 'index'])->name('admin.ami.upt_standar_mutu');
    Route::get('/admin/ami/pemetaan-standar/{upt_id}/{periode_id}', [UptStandarMutuController::class, 'detail'])->name('admin.upt_standar_mutu.detail');
    Route::post('/admin/ami/pemetaan-standar-mutu/tambah', [UptStandarMutuController::class, 'tambah'])->name('admin.upt_standar_mutu.tambah');
    Route::put('/admin/ami/pemetaan-standar-mutu/edit', [UptStandarMutuController::class, 'edit'])->name('admin.upt_standar_mutu.edit');
    Route::delete('/admin/ami/pemetaan-standar-mutu/hapus', [UptStandarMutuController::class, 'hapus'])->name('admin.upt_standar_mutu.hapus');
    Route::post('/admin/ami/pemetaan-standar/copy-periode', [UptStandarMutuController::class, 'copyPeriode'])->name('admin.upt_standar_mutu.copy_periode');
    Route::get('/admin/ami/upt-standar-mutu/get-upt-by-periode/{periode_id}', [UptStandarMutuController::class, 'getUptByPeriode'])->name('admin.upt_standar_mutu.get_upt_by_periode');
    Route::post('/admin/ami/pemetaan-standar/import', [UptStandarMutuController::class, 'import'])->name('admin.upt_standar_mutu.import');
    Route::get('/admin/ami/pemetaan-standar/export/{upt_id}/{periode_id}', [UptStandarMutuController::class, 'export'])->name('admin.upt_standar_mutu.export');

    Route::post('/admin/ami/upt-sub-standar-mutu/tambah', [UptSubStandarMutuController::class, 'tambah'])->name('admin.ami.upt_sub_standar_mutu.tambah');
    Route::post('/admin/ami/upt-sub-standar-mutu/edit', [UptSubStandarMutuController::class, 'edit'])->name('admin.ami.upt_sub_standar_mutu.edit');
    Route::post('/admin/ami/upt-sub-standar-mutu/hapus', [UptSubStandarMutuController::class, 'hapus'])->name('admin.ami.upt_sub_standar_mutu.hapus');

    Route::post('/admin/ami/upt-item-sub-standar-mutu/tambah', [UptItemSubStandarMutuController::class, 'tambah'])->name('admin.ami.upt_item_sub_standar_mutu.tambah');
    Route::post('/admin/ami/upt-item-sub-standar-mutu/edit', [UptItemSubStandarMutuController::class, 'edit'])->name('admin.ami.upt_item_sub_standar_mutu.edit');
    Route::post('/admin/ami/upt-item-sub-standar-mutu/hapus', [UptItemSubStandarMutuController::class, 'hapus'])->name('admin.ami.upt_item_sub_standar_mutu.hapus');

    // ROUTE PENUGASAN
    Route::get('/admin/ami/penugasan', [PenugasanController::class, 'index'])->name('admin.ami.penugasan');
    Route::get('/admin/ami/penugasan/detial/{id}', [PenugasanController::class, 'detail'])->name('admin.ami.penugasan.detail');
    Route::post('/admin/ami/penugasan/tambah', [PenugasanController::class, 'tambah'])->name('admin.ami.penugasan.tambah');
    Route::put('/admin/ami/penugasan/edit', [PenugasanController::class, 'edit'])->name('admin.ami.penugasan.edit');
    Route::put('/admin/ami/penugasan/aktifkan/{id}', [PenugasanController::class, 'aktifkan'])->name('admin.ami.penugasan.aktifkan');
    Route::get('/admin/ami/penugasan/export/{id}', [PenugasanController::class, 'exportPdf'])->name('admin.ami.penugasan.export');

    Route::get('/admin/monitoring-tindakan-koreksi', [MonitoringTindakanKoreksiController::class, 'index'])->name('admin.monitoring_tk.index');
    Route::get('/admin/monitoring-tindakan-koreksi/bukti/{tindakan_koreksi_id}/preview', [MonitoringTindakanKoreksiController::class, 'previewBukti'])->name('admin.monitoring_tk.preview_bukti');
    Route::get('/admin/monitoring-tindakan-koreksi/bukti/{tindakan_koreksi_id}/download', [MonitoringTindakanKoreksiController::class, 'downloadBukti'])->name('admin.monitoring_tk.download_bukti');
    Route::get('/admin/monitoring-tindakan-koreksi/{penugasan_id}/export', [MonitoringTindakanKoreksiController::class, 'export'])->name('admin.monitoring_tk.export');
    Route::get('/admin/monitoring-tindakan-koreksi/{penugasan_id}', [MonitoringTindakanKoreksiController::class, 'show'])->name('admin.monitoring_tk.show');

    Route::get('/admin/rka', [AdminRkaController::class, 'index'])->name('admin.rka.index');
    Route::get('/admin/rka/{penugasan_id}/export', [AdminRkaController::class, 'export'])->name('admin.rka.export');
    Route::get('/admin/rka/{penugasan_id}', [AdminRkaController::class, 'show'])->name('admin.rka.show');
});

Route::middleware(['auth', 'checkRole:kepala_p4mp'])->group(function () {
    Route::get('/kepala-p4mp/dashboard', [KepalaP4mpDashboardController::class, 'index'])->name('kepala_p4mp.dashboard');
    Route::get('/kepala-p4mp/rka', [AdminRkaController::class, 'index'])->name('kepala_p4mp.rka.index');
    Route::get('/kepala-p4mp/rka/{penugasan_id}/export', [AdminRkaController::class, 'export'])->name('kepala_p4mp.rka.export');
    Route::get('/kepala-p4mp/rka/{penugasan_id}', [AdminRkaController::class, 'show'])->name('kepala_p4mp.rka.show');
    Route::get('/kepala-p4mp/tindakan-koreksi', [MonitoringTindakanKoreksiController::class, 'index'])->name('kepala_p4mp.tindakan_koreksi.index');
    Route::get('/kepala-p4mp/tindakan-koreksi/bukti/{tindakan_koreksi_id}/preview', [MonitoringTindakanKoreksiController::class, 'previewBukti'])->name('kepala_p4mp.tindakan_koreksi.preview_bukti');
    Route::get('/kepala-p4mp/tindakan-koreksi/bukti/{tindakan_koreksi_id}/download', [MonitoringTindakanKoreksiController::class, 'downloadBukti'])->name('kepala_p4mp.tindakan_koreksi.download_bukti');
    Route::post('/kepala-p4mp/tindakan-koreksi/{penugasan_id}/finalisasi', [MonitoringTindakanKoreksiController::class, 'finalisasi'])->name('kepala_p4mp.tindakan_koreksi.finalisasi');
    Route::get('/kepala-p4mp/tindakan-koreksi/{penugasan_id}/export', [MonitoringTindakanKoreksiController::class, 'export'])->name('kepala_p4mp.tindakan_koreksi.export');
    Route::get('/kepala-p4mp/tindakan-koreksi/{penugasan_id}', [MonitoringTindakanKoreksiController::class, 'show'])->name('kepala_p4mp.tindakan_koreksi.show');
});

Route::middleware(['auth', 'checkRole:auditor'])->group(function () {
    Route::get('/auditor/dashboard', [AuditorController::class, 'index'])->name('auditor.dashboard');

    // ROUTE PENUGASAN
    Route::get('/auditor/penugasan', [AuditorPenugasanController::class, 'index'])->name('auditor.penugasan');
    Route::get('/auditor/penugasan/ajukan', [AuditorPenugasanController::class, 'ajukan'])->name('auditor.penugasan.ajukan');
    Route::post('/auditor/penugasan/setuju', [AuditorPenugasanController::class, 'setuju'])->name('auditor.penugasan.setuju');
    Route::post('/auditor/penugasan/tolak', [AuditorPenugasanController::class, 'tolak'])->name('auditor.penugasan.tolak');
    
    // ROUTE PELAKSANAN AUDIT
    Route::get('/auditor/pelaksanaan-audit', [PelaksanaanAuditController::class, 'index'])->name('auditor.pelaksanaan_audit');
    Route::get('/auditor/pelaksanaan-audit/detail/{id}', [PelaksanaanAuditController::class, 'detail'])->name('auditor.pelaksanaan_audit.detail');
    Route::post('/auditor/pelaksanaan-audit/penilaian/{id}', [PelaksanaanAuditController::class, 'penilaian'])->name('auditor.pelaksanaan_audit.penilaian');
    Route::get('/auditor/pelaksanaan-audit/exportRka/{id}', [PelaksanaanAuditController::class, 'exportRka'])->name('auditor.pelaksanaan_audit.exportRka');
    
    // ROUTE LIHAT BUKTI DUKUNG
    Route::get('/auditor/pelaksanaan-audit/preview-bukti/{id}', [PelaksanaanAuditController::class, 'previewBukti'])->name('auditor.bukti_dukung.preview');
    Route::get('/auditor/pelaksanaan-audit/download-bukti/{id}', [PelaksanaanAuditController::class, 'downloadBukti'])->name('auditor.bukti_dukung.download');

    // ROUTE RKA
    Route::get('/auditor/rka', [AuditorRkaController::class, 'index'])->name('auditor.rka.index');
    Route::get('/auditor/rka/{rka_id}/export', [AuditorRkaController::class, 'export'])->name('auditor.rka.export');
    Route::get('/auditor/rka/{penugasan_id}', [AuditorRkaController::class, 'show'])->name('auditor.rka.show');
    Route::patch('/auditor/rka/{rka_id}', [AuditorRkaController::class, 'update'])->name('auditor.rka.update');

    // ROUTE TINDAKAN KOREKSI
    Route::get('/auditor/tindakan-koreksi', [AuditorTindakanKoreksiController::class, 'index'])->name('auditor.tindakan_koreksi.index');
    Route::get('/auditor/tindakan-koreksi/bukti/{tindakan_koreksi_id}/preview', [AuditorTindakanKoreksiController::class, 'previewBukti'])->name('auditor.tindakan_koreksi.preview_bukti');
    Route::get('/auditor/tindakan-koreksi/bukti/{tindakan_koreksi_id}/download', [AuditorTindakanKoreksiController::class, 'downloadBukti'])->name('auditor.tindakan_koreksi.download_bukti');
    Route::get('/auditor/tindakan-koreksi/{penugasan_id}/export', [AuditorTindakanKoreksiController::class, 'export'])->name('auditor.tindakan_koreksi.export');
    Route::get('/auditor/tindakan-koreksi/{penugasan_id}', [AuditorTindakanKoreksiController::class, 'show'])->name('auditor.tindakan_koreksi.show');
    Route::post('/auditor/tindakan-koreksi/{penugasan_id}/{jawaban_audit_id}', [AuditorTindakanKoreksiController::class, 'rumuskan'])->name('auditor.tindakan_koreksi.rumuskan');
    Route::patch('/auditor/tindakan-koreksi/verifikasi/{tindakan_koreksi_id}', [AuditorTindakanKoreksiController::class, 'verifikasi'])->name('auditor.tindakan_koreksi.verifikasi');

    
});

Route::middleware(['auth', 'checkRole:auditee'])->group(function () {
    Route::get('/auditee/dashboard', [RoleAuditeeController::class, 'index'])->name('auditee.dashboard');

    // ROUTE KELOLA AKUN DOSEN
    Route::get('/auditee/dosen', [DataDosenController::class, 'index'])->name('auditee.dosen');
    Route::post('/auditee/dosen/tambah', [DataDosenController::class, 'tambah'])->name('auditee.dosen.tambah');
    Route::put('/auditee/dosen/edit', [DataDosenController::class, 'edit'])->name('auditee.dosen.edit');
    Route::delete('/auditee/dosen/hapus', [DataDosenController::class, 'hapus'])->name('auditee.dosen.hapus');

    // ROUTE PENUGASAN
    Route::get('/auditee/penugasan', [AuditeePenugasanController::class, 'index'])->name('auditee.penugasan');
    Route::post('/auditee/penugasan/ajukan', [AuditeePenugasanController::class, 'ajukan'])->name('auditee.penugasan.ajukan');
    Route::post('/auditee/penugasan/setuju', [AuditeePenugasanController::class, 'setuju'])->name('auditee.penugasan.setuju');
    Route::post('/auditee/penugasan/tolak', [AuditeePenugasanController::class, 'tolak'])->name('auditee.penugasan.tolak');

    Route::get('/auditee/ami', [StandarAMIController::class, 'index'])->name('auditee.ami');
    Route::get('/auditee/ami/detail/{upt_id}/{periode_id}', [StandarAMIController::class, 'detail'])->name('auditee.ami.detail');
    Route::post('/auditee/ami/item-dosen/{penugasan_id}', [StandarAMIController::class, 'updateItemDosen'])->name('auditee.item_dosen.update');
    Route::post('/auditee/ami/bukti-dukung/upload', [StandarAMIController::class, 'uploadBukti'])->name('auditee.bukti_dukung.upload');
    Route::patch('/auditee/ami/bukti-dukung/{id}/validasi', [StandarAMIController::class, 'validasiBukti'])->name('auditee.bukti_dukung.validasi');
    Route::delete('/auditee/ami/bukti-dukung/{id}', [StandarAMIController::class, 'hapusBukti'])->name('auditee.bukti_dukung.hapus');
    Route::get('/auditee/ami/bukti-dukung/{id}/download', [StandarAMIController::class, 'downloadBukti'])->name('auditee.bukti_dukung.download');
    Route::get('/auditee/ami/bukti-dukung/{id}/preview', [StandarAMIController::class, 'previewBukti'])->name('auditee.bukti_dukung.preview');

    // ROUTE RKA
    Route::get('/auditee/rka', [RkaController::class, 'index'])->name('auditee.rka.index');
    Route::get('/auditee/rka/{penugasan_id}', [RkaController::class, 'show'])->name('auditee.rka.show');
    Route::get('/auditee/rka/{penugasan_id}/export', [RkaController::class, 'export'])->name('auditee.rka.export');

    // ROUTE TINDAKAN KOREKSI
    Route::get('/auditee/tindakan-koreksi', [AuditeeTindakanKoreksiController::class, 'index'])->name('auditee.tindakan_koreksi.index');
    Route::post('/auditee/tindakan-koreksi/bukti/{tindakan_koreksi_id}', [AuditeeTindakanKoreksiController::class, 'uploadBukti'])->name('auditee.tindakan_koreksi.upload_bukti');
    Route::get('/auditee/tindakan-koreksi/bukti/{tindakan_koreksi_id}/preview', [AuditeeTindakanKoreksiController::class, 'previewBukti'])->name('auditee.tindakan_koreksi.preview_bukti');
    Route::get('/auditee/tindakan-koreksi/bukti/{tindakan_koreksi_id}/download', [AuditeeTindakanKoreksiController::class, 'downloadBukti'])->name('auditee.tindakan_koreksi.download_bukti');
    Route::patch('/auditee/tindakan-koreksi/{tindakan_koreksi_id}/dokumen-dosen', [AuditeeTindakanKoreksiController::class, 'aturKebutuhanDokumenDosen'])->name('auditee.tindakan_koreksi.dokumen_dosen.atur');
    Route::patch('/auditee/tindakan-koreksi/dokumen-dosen/{dokumen_id}/validasi', [AuditeeTindakanKoreksiController::class, 'validasiDokumenDosen'])->name('auditee.tindakan_koreksi.dokumen_dosen.validasi');
    Route::get('/auditee/tindakan-koreksi/dokumen-dosen/{dokumen_id}/preview', [AuditeeTindakanKoreksiController::class, 'previewDokumenDosen'])->name('auditee.tindakan_koreksi.dokumen_dosen.preview');
    Route::get('/auditee/tindakan-koreksi/dokumen-dosen/{dokumen_id}/download', [AuditeeTindakanKoreksiController::class, 'downloadDokumenDosen'])->name('auditee.tindakan_koreksi.dokumen_dosen.download');
    Route::get('/auditee/tindakan-koreksi/{penugasan_id}/export', [AuditeeTindakanKoreksiController::class, 'export'])->name('auditee.tindakan_koreksi.export');
    Route::get('/auditee/tindakan-koreksi/{penugasan_id}', [AuditeeTindakanKoreksiController::class, 'show'])->name('auditee.tindakan_koreksi.show');
});

Route::middleware(['auth', 'checkRole:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [RoleDosenController::class, 'index'])->name('dosen.dashboard');
    Route::get('/dosen/bukti-dukung', [RoleDosenController::class, 'dokumen'])->name('dosen.bukti_dukung.index');
    Route::post('/dosen/bukti-dukung/upload', [RoleDosenController::class, 'uploadDokumen'])->name('dosen.bukti_dukung.upload');
    Route::delete('/dosen/bukti-dukung/{id}', [RoleDosenController::class, 'hapusDokumen'])->name('dosen.bukti_dukung.hapus');
    Route::get('/dosen/bukti-dukung/{id}/preview', [RoleDosenController::class, 'previewDokumen'])->name('dosen.bukti_dukung.preview');
    Route::get('/dosen/bukti-dukung/{id}/download', [RoleDosenController::class, 'downloadDokumen'])->name('dosen.bukti_dukung.download');
    Route::get('/dosen/tindakan-koreksi-dokumen', [RoleDosenController::class, 'dokumenTindakanKoreksi'])->name('dosen.tindakan_koreksi_dokumen.index');
    Route::post('/dosen/tindakan-koreksi-dokumen/{tindakan_koreksi_id}', [RoleDosenController::class, 'uploadDokumenTindakanKoreksi'])->name('dosen.tindakan_koreksi_dokumen.upload');
    Route::delete('/dosen/tindakan-koreksi-dokumen/{dokumen_id}', [RoleDosenController::class, 'hapusDokumenTindakanKoreksi'])->name('dosen.tindakan_koreksi_dokumen.hapus');
    Route::get('/dosen/tindakan-koreksi-dokumen/{dokumen_id}/preview', [RoleDosenController::class, 'previewDokumenTindakanKoreksi'])->name('dosen.tindakan_koreksi_dokumen.preview');
    Route::get('/dosen/tindakan-koreksi-dokumen/{dokumen_id}/download', [RoleDosenController::class, 'downloadDokumenTindakanKoreksi'])->name('dosen.tindakan_koreksi_dokumen.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'buka'])->name('notifikasi.buka');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca_semua');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
