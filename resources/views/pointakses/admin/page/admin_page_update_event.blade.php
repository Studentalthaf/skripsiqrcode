{-- Simpan sebagai resources/views/pointakses/admin/event/edit.blade.php --}}
@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="600">
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Acara</h3>
                    </div>
                    <form action="{{ route('admin.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Judul Acara</label>
                                <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $event->title) }}" placeholder="Masukkan judul acara" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea name="description" class="form-control" id="description" rows="4" placeholder="Deskripsi acara (opsional)">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="date">Tanggal Acara</label>
                                <input type="date" name="date" class="form-control" id="date" value="{{ old('date', $event->date) }}" required>
                                @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="type_event">Jenis Acara</label>
                                <input type="text" name="type_event" class="form-control" id="type_event" value="{{ old('type_event', $event->type_event) }}" placeholder="Masukkan jenis acara" required>
                                @error('type_event')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="faculty_id">Fakultas</label>
                                <select name="faculty_id" class="form-control" id="faculty_id" required>
                                    <option value="">Pilih Fakultas</option>
                                    @foreach(\App\Models\User::where('role', 'fakultas')->get() as $faculty)
                                        <option value="{{ $faculty->id }}" {{ (old('faculty_id', $event->faculty_id) == $faculty->id) ? 'selected' : '' }}>
                                            {{ $faculty->nama_lengkap }} ({{ $faculty->unit_kerja ?? 'Tidak ada unit kerja' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="logo">Logo Acara</label>
                                @if($event->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $event->logo) }}" alt="Logo" style="max-height: 100px">
                                        <p class="small text-muted">Logo saat ini</p>
                                    </div>
                                @endif
                                <input type="file" name="logo" class="form-control-file" id="logo" accept="image/*">
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah logo</small>
                                @error('logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="signature">Tanda Tangan</label>
                                @if($event->signature)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $event->signature) }}" alt="Tanda Tangan" style="max-height: 100px">
                                        <p class="small text-muted">Tanda tangan saat ini</p>
                                    </div>
                                @endif
                                <input type="file" name="signature" class="form-control-file" id="signature" accept="image/*">
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah tanda tangan</small>
                                @error('signature')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="template_pdf">Template Sertifikat (PDF)</label>
                                @if($event->template_pdf)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $event->template_pdf) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-pdf"></i> Lihat Template Saat Ini
                                        </a>
                                    </div>
                                @endif
                                <input type="file" name="template_pdf" class="form-control-file" id="template_pdf" accept="application/pdf">
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah template</small>
                                @error('template_pdf')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('admin.event') }}" class="btn btn-secondary">Kembali ke Daftar Acara</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">Informasi Tambahan</h3>
                    </div>
                    <div class="card-body">
                        <p>Pastikan semua informasi acara yang diinput sudah benar sebelum menyimpan perubahan.</p>
                        <ul>
                            <li>Judul acara harus unik.</li>
                            <li>Jenis acara harus sesuai dengan format yang diinginkan.</li>
                            <li>Logo dan tanda tangan harus dalam format gambar.</li>
                            <li>Template sertifikat harus dalam format PDF.</li>
                            <li>File yang tidak diubah akan tetap menggunakan file yang sudah ada.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pointakses.admin.include.sidebar_admin')
@endsection