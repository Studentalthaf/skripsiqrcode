@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 d-flex justify-content-between align-items-center">
                <div class="col-12 col-md text-center text-md-left">
                    <h3 class="font-weight-bold">Peserta </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Acara</li>
                        <li class="breadcrumb-item active">Peserta</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <!-- Header Card -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Peserta</h3>
                <!-- Tombol Tambah Peserta -->
                <a href="{{ route('admin.create.participant', ['event_id' => $event_id]) }}"
                    class="btn btn-success btn-sm">
                    Tambah Peserta
                </a>
            </div>

            <!-- Body Card -->
            <div class="card-body p-0">
                @if($participants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nama Peserta</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Tanda Tangan</th>
                                <th>Logo</th>
                                <th style="width: 220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $participant->decrypted_name }}</td>
                                <td>{{ $participant->decrypted_email }}</td>
                                <td>{{ $participant->decrypted_phone }}</td>
                                <td>
                                    <img src="{{ $participant->decrypted_signature ? Storage::url($participant->decrypted_signature) : asset('images/default-signature.png') }}"
                                        alt="Tanda Tangan" style="width: 50px; height: auto;">
                                </td>
                                <td>
                                    <img src="{{ $participant->decrypted_logo ? Storage::url($participant->decrypted_logo) : asset('images/default-logo.png') }}"
                                        alt="Logo" style="width: 50px; height: auto;">
                                </td>
                                <td class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.show.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        class="btn btn-primary btn-sm" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.edit.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.destroy.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                            onclick="return confirm('Anda yakin ingin menghapus peserta ini?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                    @if($participant->decrypted_name !== "Gagal Dekripsi")
                                    <a href="{{ route('participant.qrcode.download', $participant->id) }}" class="btn btn-sm btn-success">
                                        Download QR Code
                                    </a>
                                    <a href="{{ route('admin.view.certificate', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        class="btn btn-success btn-sm" title="Lihat Sertifikat">
                                        <i class="fas fa-file-pdf"></i> Lihat Sertifikat
                                    </a>

                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="p-3">Belum ada peserta yang terdaftar.</p>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@include('pointakses.admin.include.sidebar_admin')
@endsection