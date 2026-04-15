<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AkunDosenController;
use App\Http\Controllers\Admin\Akun\AuditeeController;
use App\Http\Controllers\Admin\Akun\AuditorController as AdminAkunAuditorController;
use App\Http\Controllers\Admin\Akun\DosenController;
use App\Http\Controllers\Admin\Ami\ItemSubStandarMutuController;
use App\Http\Controllers\Admin\Ami\StandarMutuController;
use App\Http\Controllers\Admin\Ami\SubStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptItemSubStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptStandarMutuController;
use App\Http\Controllers\Admin\Ami\UptSubStandarMutuController;
use App\Http\Controllers\Admin\Data\ProdiController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\Data\UPTController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Auditor\AuditorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route umum setelah login
Route::get('/dashboard', function () {
    return view('dashboard');
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
    Route::get('/admin/ami/pemetaan-standar/{upt_id}', [UptStandarMutuController::class, 'detail'])->name('admin.upt_standar_mutu.detail');
    Route::post('/admin/ami/pemetaan-standar-mutu/tambah', [UptStandarMutuController::class, 'tambah'])->name('admin.upt_standar_mutu.tambah');
    Route::put('/admin/ami/pemetaan-standar-mutu/edit', [UptStandarMutuController::class, 'edit'])->name('admin.upt_standar_mutu.edit');
    Route::delete('/admin/ami/pemetaan-standar-mutu/hapus', [UptStandarMutuController::class, 'hapus'])->name('admin.upt_standar_mutu.hapus');

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
});

Route::middleware(['auth', 'checkRole:auditor'])->group(function () {
    Route::get('/auditor/dashboard', [AuditorController::class, 'index'])->name('auditor.dashboard');
});

Route::middleware(['auth', 'checkRole:auditee'])->group(function () {
    Route::get('/auditee/dashboard', [AuditeeController::class, 'index'])->name('auditee.dashboard');
});

Route::middleware(['auth', 'checkRole:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'index'])->name('dosen.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
