@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="600">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Acara</h3>
                    </div>
                    <form action="{{ route('admin.event.store') }}" method="POST" enctype="multipart/form-data">

                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul Acara</label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="Masukkan judul acara" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea name="description" class="form-control" id="description" rows="4" placeholder="Deskripsi acara (opsional)"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="date">Tanggal Acara</label>
                                <input type="date" name="date" class="form-control" id="date" required>
                            </div>

                            <div class="form-group">
                                <label for="type_event">Jenis Acara</label>
                                <input type="text" name="type_event" class="form-control" id="type_event" placeholder="Masukkan jenis acara" required>
                            </div>

                            <div class="form-group">
                                <label for="logo">Logo Acara</label>
                                <input type="file" name="logo" class="form-control-file" id="logo" accept="image/*">
                            </div>

                            <div class="form-group">
                                <label for="signature">Tanda Tangan</label>
                                <input type="file" name="signature" class="form-control-file" id="signature" accept="image/*">
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.event') }}" class="btn btn-secondary">Kembali ke Daftar Acara</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pointakses.admin.include.sidebar_admin')
@endsection
