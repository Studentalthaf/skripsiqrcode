<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController; // Mengimpor AdminController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

// Rute untuk halaman depan
Route::view('/', 'halaman_depan.index');

// Rute untuk login, registrasi, dan verifikasi
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/reg', [AuthController::class, 'create'])->name('registrasi');
Route::post('/reg', [AuthController::class, 'register']);
Route::get('/verify/{verify_key}', [AuthController::class, 'verify']);
Route::middleware(['auth'])->group(function() {

    // Rute untuk halaman dashboard admin, hanya bisa diakses oleh admin
    Route::get('/admin', [AdminController::class, 'index'])->middleware('userAkses:admin')->name('admin.index');

    // Rute untuk halaman dashboard user, hanya bisa diakses oleh user
    Route::get('/user', [UserController::class, 'index'])->middleware('userAkses:user')->name('user.index');
    Route::get('/user/acara',[EventController::class,'acara'])->middleware('userAkses:user')->name('user.acara');
    Route::get('/user/acara/tambah', [EventController::class, 'tambah'])->middleware('userAkses:user')->name('user.acara.tambah');

    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
