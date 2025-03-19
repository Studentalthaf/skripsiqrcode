@extends('pointakses.admin.layouts.dashboard')
@section('content')
<div class="container">
    <h2>Edit Peserta</h2>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.update.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_peserta">Nama Peserta</label>
            <input type="text" class="form-control" id="nama_peserta" name="nama_peserta" value="{{ old('nama_peserta', $decryptedData['name']) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $decryptedData['email']) }}" required>
        </div>

        <div class="form-group">
            <label for="telepon">Telepon</label>
            <input type="text" class="form-control" id="telepon" name="telepon" value="{{ old('telepon', $decryptedData['phone']) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.index.participant', ['event_id' => $event_id]) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@include('pointakses.admin.include.sidebar_admin')
@endsection
