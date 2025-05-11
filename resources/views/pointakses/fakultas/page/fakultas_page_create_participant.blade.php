@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Peserta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fakultas.event') }}">Acara</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fakultas.index.participant', ['event_id' => $event_id]) }}">Peserta</a></li>
                        <li class="breadcrumb-item active">Tambah Peserta</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Peserta</h3>
                </div>
                <form action="{{ route('fakultas.store.participant', ['event_id' => $event_id]) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="user_id">Pilih Peserta</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">-- Pilih Peserta --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama_lengkap }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Tambah Peserta</button>
                        <a href="{{ route('fakultas.index.participant', ['event_id' => $event_id]) }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection