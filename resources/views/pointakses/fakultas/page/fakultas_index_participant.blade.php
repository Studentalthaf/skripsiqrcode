@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <h3 class="font-weight-bold">Daftar Peserta</h3>
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fakultas.event') }}">Acara</a></li>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Peserta</h3>
                <a href="{{ route('fakultas.create.participant', ['event_id' => $event_id]) }}"
                    class="btn btn-success btn-sm">Tambah Peserta</a>
            </div>
            <div class="card-body p-0">
                @if($participants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $participant)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $participant->decrypted_name ?? 'Gagal Dekripsi' }}</td>
                                <td>{{ $participant->decrypted_email ?? '-' }}</td>
                                <td>{{ $participant->decrypted_phone ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('fakultas.view.certificate', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        class="btn btn-success btn-sm" title="Lihat Sertifikat">
                                        <i class="fas fa-file-pdf"></i> Lihat Sertifikat
                                    </a>
                                    <a href="{{ route('fakultas.download.qrcode', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        class="btn btn-primary btn-sm" title="Unduh QR Code">
                                        <i class="fas fa-qrcode"></i> QR Code
                                    </a>
                                    <form action="{{ route('fakultas.destroy.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                        method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus Peserta">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="p-3">Belum ada peserta untuk acara ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection