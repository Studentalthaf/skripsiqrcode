<?php

use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestController;

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
    Route::get('/user/acara/hapus/{id}', [EventController::class, 'hapus'])->middleware('userAkses:user')->name('user.acara.hapus');
    Route::post('/user/acara/store', [EventController::class, 'store'])->middleware('userAkses:user')->name('event.store');
    Route::get('/user/acara/edit/{id}', [EventController::class, 'edit'])->middleware('userAkses:user')->name('user.acara.edit');
    Route::put('/user/acara/update/{id}', [EventController::class, 'update'])->middleware('userAkses:user')->name('user.acara.update');

    // Route untuk peserta (participants)
    Route::get('/user/acara/{event_id}/participants', [ParticipantController::class, 'index'])->name('user.participant.index');

    // Menampilkan form untuk menambah peserta
    Route::get('user/acara/{event_id}/participants/create', [ParticipantController::class, 'create'])->name('user.participant.create');

    // Menyimpan data peserta baru
    Route::post('user/acara/{event_id}/participants/create', [ParticipantController::class, 'store'])->name('user.participant.store');

    // Rute untuk mengedit peserta
    Route::get('/user/acara/{event_id}/participants/edit/{participant_id}', [ParticipantController::class, 'edit'])->name('user.participant.edit');

    // Rute untuk memperbarui peserta
    Route::put('/user/acara/{event_id}/participants/update/{participant_id}', [ParticipantController::class, 'update'])->name('user.participant.update');

    // Rute untuk menghapus peserta

    Route::delete('/user/acara/{event_id}/participants/destroy/{participant_id}', [ParticipantController::class, 'destroy'])->name('user.participant.destroy');

    Route::get('/user/test', [TestController::class, 'index'])->name('user.test');

    Route::get('/user/acara/{event_id}/participant/{participant_id}', [ParticipantController::class, 'show'])->name('user.participant.show');

    Route::get('/participant/{event_id}/{participant_id}/qrcode', [ParticipantController::class, 'generateQRCode'])->name('user.participant.qrcode');
    
    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
