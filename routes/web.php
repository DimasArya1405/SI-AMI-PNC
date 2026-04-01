<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AkunAuditorController;
use App\Http\Controllers\AkunDosenController;
use App\Http\Controllers\Admin\Akun\AuditeeController;
use App\Http\Controllers\Admin\Akun\AuditorController as AdminAkunAuditorController;
use App\Http\Controllers\Admin\Akun\DosenController;
use App\Http\Controllers\Admin\Data\ProdiController;
use App\Http\Controllers\AuditorController;
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

require __DIR__.'/auth.php';
