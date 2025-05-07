<?php

use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\DecryptionController;

// Rute untuk halaman depan
Route::view('/', 'halaman_depan.index');

// Rute untuk login, registrasi, dan verifikasi
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/reg', [AuthController::class, 'create'])->name('registrasi');
Route::post('/reg', [AuthController::class, 'register']);

Route::middleware(['auth'])->group(function () {

    // Rute untuk halaman dashboard admin
    Route::get('/admin', [AdminController::class, 'index'])->middleware('userAkses:admin')->name('admin.index');
    Route::get('/admin/event', [AdminController::class, 'event'])->middleware('userAkses:admin')->name('admin.event');
    Route::get('/admin/event/create', [AdminController::class, 'create_event'])->middleware('userAkses:admin')->name('admin.create.event');
    Route::post('/admin/event/store', [AdminController::class, 'store'])->middleware('userAkses:admin')->name('admin.event.store');
    Route::get('/admin/event/delete/{id}', [AdminController::class, 'delete_event'])->middleware('userAkses:admin')->name('admin.event.delete');
    Route::get('/admin/event/edit/{id}', [AdminController::class, 'edit_event'])->middleware('userAkses:admin')->name('admin.event.edit');
    Route::put('/admin/event/update/{id}', [AdminController::class, 'update'])->middleware('userAkses:admin')->name('admin.event.update');
    Route::get('/admin/event/placeholder/{id}', [AdminController::class, 'placeholder'])->name('admin.event.placeholder');
    Route::post('/admin/pdf/{id}/save-placeholder', [AdminController::class, 'savePlaceholder'])->name('pdf.save');
    Route::get('admin/event/{event_id}/participant/{participant_id}/certificate', [AdminController::class, 'viewCertificate'])->name('admin.view.certificate');
    Route::get('/admin/users', [AdminController::class, 'users'])->middleware('userAkses:admin')->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'admin_user_store'])->middleware('userAkses:admin')->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'admin_edit_user'])->middleware('userAkses:admin')->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'admin_update_user'])->middleware('userAkses:admin')->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'admin_destroy_user'])->middleware('userAkses:admin')->name('admin.users.destroy');
    Route::get('/admin/event/{event_id}/participants', [AdminController::class, 'index_participant'])->middleware('userAkses:admin')->name('admin.index.participant');
    Route::get('admin/event/{event_id}/participants/create', [AdminController::class, 'create_participant'])->middleware('userAkses:admin')->name('admin.create.participant');
    Route::post('admin/event/{event_id}/participants/store', [AdminController::class, 'store_participant'])->middleware('userAkses:admin')->name('admin.store.participant');
    Route::get('/admin/event/{event_id}/participants/edit/{participant_id}', [AdminController::class, 'edit_participant'])->middleware('userAkses:admin')->name('admin.edit.participant');
    Route::put('/admin/event/{event_id}/participants/update/{participant_id}', [AdminController::class, 'update_participant'])->middleware('userAkses:admin')->name('admin.update.participant');
    Route::get('/admin/event/{event_id}/participant/{participant_id}', [AdminController::class, 'show_participant'])->middleware('userAkses:admin')->name('admin.show.participant');
    Route::delete('/admin/event/{event_id}/participants/destroy/{participant_id}', [AdminController::class, 'destroy_participant'])->middleware('userAkses:admin')->name('admin.destroy.participant');
    Route::get('/participant/{id}/qrcode/download', [AdminController::class, 'downloadQRCode'])->name('participant.qrcode.download')->middleware('auth');
    Route::get('/admin/test', [TestController::class, 'index'])->name('admin.test');
    Route::post('/admin/test/scan', [TestController::class, 'scan'])->name('qr.scan');




    // Rute untuk halaman dashboard user
    Route::get('/user', [UserController::class, 'index'])->middleware('userAkses:user')->name('user.index');
    Route::get('/user/history_event', [ParticipantController::class, 'history'])->middleware('userAkses:user')->name('user.history');
    Route::get('/user/acara/tambah', [EventController::class, 'tambah'])->middleware('userAkses:user')->name('user.acara.tambah');
    Route::get('/user/acara/hapus/{id}', [EventController::class, 'hapus'])->middleware('userAkses:user')->name('user.acara.hapus');
    Route::post('/user/acara/store', [EventController::class, 'store'])->middleware('userAkses:user')->name('event.store');
    Route::get('/user/acara/edit/{id}', [EventController::class, 'edit'])->middleware('userAkses:user')->name('user.acara.edit');
    Route::put('/user/acara/update/{id}', [EventController::class, 'update'])->middleware('userAkses:user')->name('user.acara.update');


    Route::get('/fakultas', [FakultasController::class, 'index'])->middleware('userAkses:fakultas')->name('fakultas.index');



    // Rute untuk logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Route::post('/decrypt-qr', [DecryptionController::class, 'decryptQr'])->name('decrypt.qr');
});
