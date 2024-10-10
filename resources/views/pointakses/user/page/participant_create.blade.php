@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="600">
    <div class="container mt-5">
    <h2>Tambah Peserta untuk Event: {{ $event->nama_event }}</h2>

    <form action="#" method="POST">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}"> <!-- Mengirim event_id dengan form -->

        <div class="form-group">
            <label for="nama_peserta">Nama Peserta</label>
            <input type="text" name="nama_peserta" class="form-control" placeholder="Nama Peserta" required>
        </div>

        <div class="form-group">
            <label for="instansi">Instansi</label>
            <input type="text" name="instansi" class="form-control" placeholder="Instansi">
        </div>

        <div class="form-group">
            <label for="serial_number">Serial Number</label>
            <input type="text" name="serial_number" class="form-control" placeholder="Serial Number" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@include('pointakses.user.include.sidebar_user')
@endsection
