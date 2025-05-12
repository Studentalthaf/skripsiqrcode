@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3 align-items-center">
                <div class="col-sm-6">
                    <h3 class="font-weight-bold text-dark">Daftar Peserta</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fakultas.event') }}">Acara</a></li>
                        <li class="breadcrumb-item active">Peserta</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- Participants Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Peserta</h3>
                    <a href="{{ route('fakultas.create.participant', ['event_id' => $event_id]) }}"
                        class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Peserta
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($participants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('fakultas.view.certificate', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                                class="btn btn-success btn-sm" title="Lihat Sertifikat">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('fakultas.download.qrcode', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                                class="btn btn-primary btn-sm" title="Unduh QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <form action="{{ route('fakultas.destroy.participant', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}"
                                                method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Peserta">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-users-slash fa-2x mb-2"></i>
                        <p>Belum ada peserta untuk acara ini.</p>
                    </div>
                    @endif
                </div>
                <!-- Card Footer for Pagination (if needed) -->
                @if($participants->hasPages())
                <div class="card-footer">
                    {{ $participants->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection

@section('styles')
<style>
    .content-wrapper {
        background-color: #f4f6f9;
    }

    .card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
    }

    .btn-group .btn {
        margin-right: 5px;
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: scale(1.1);
    }

    .alert {
        border-radius: 0.5rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 576px) {
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .btn-group .btn {
            flex: 1;
            margin-right: 0;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Automatically close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 150);
            });
        }, 5000);
    });
</script>
@endsection