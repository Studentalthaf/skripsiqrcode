@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="600">
    <div class="container">
        <h2>Tambah Peserta</h2>

        <!-- Form untuk menambah peserta -->
        <form action="{{ route('user.participant.store', ['event_id' => $event_id]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_peserta">Nama Peserta</label>
                <input type="text" class="form-control @error('nama_peserta') is-invalid @enderror" id="nama_peserta" name="nama_peserta" value="{{ old('nama_peserta') }}" required>
                @error('nama_peserta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Peserta</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="telepon">Telepon Peserta</label>
                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon') }}" required>
                @error('telepon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Tambah Peserta</button>
                <a href="{{ route('user.participant.index', ['event_id' => $event_id]) }}" class="btn btn-secondary">Kembali ke Daftar Peserta</a>
            </div>
        </form>

    </div>
    @include('pointakses.user.include.sidebar_user')
</div>
@endsection
