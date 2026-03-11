<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AkunAuditorController;
use App\Http\Controllers\AkunDosenController;
use App\Http\Controllers\Admin\Akun\AuditeeController;
use App\Http\Controllers\Admin\Akun\AuditorController as AdminAkunAuditorController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\DosenController;
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
    Route::get('/admin/akun/dosen', [AkunDosenController::class, 'index'])->name('admin.akun.dosen');
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
