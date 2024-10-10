<?php

use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
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

Route::middleware(['auth'])->group(function () {

    // Rute untuk halaman dashboard admin
    Route::get('/admin', [AdminController::class, 'index'])->middleware('userAkses:admin')->name('admin.index');

    // Rute untuk halaman dashboard user
    Route::get('/user', [UserController::class, 'index'])->middleware('userAkses:user')->name('user.index');

    // Rute untuk acara
    Route::get('/user/acara', [EventController::class, 'acara'])->middleware('userAkses:user')->name('user.acara');
    Route::get('/user/acara/tambah', [EventController::class, 'tambah'])->middleware('userAkses:user')->name('user.acara.tambah');
    Route::get('/user/acara/hapus/{id}', [EventController::class, 'hapus'])->name('user.acara.hapus');
    Route::post('/user/acara/store', [EventController::class, 'store'])->middleware('userAkses:user')->name('event.store');
    Route::get('/user/acara/edit/{id}', [EventController::class, 'edit'])->middleware('userAkses:user')->name('user.acara.edit');
    Route::put('/user/acara/update/{id}', [EventController::class, 'update'])->middleware('userAkses:user')->name('user.acara.update');


    Route::get('/user/acara/{event_id}/participants', [ParticipantController::class, 'index'])->name('user.participant.index');

    Route::get('/user/acara/{event_id}/participants/create', [ParticipantController::class, 'create'])->name('user.participant.create');

    // Tambahkan route lainnya sesuai kebutuhan...

    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
