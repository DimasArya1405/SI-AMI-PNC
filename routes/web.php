<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditeeController;
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
