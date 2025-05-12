```blade
@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="container mt-4">
        <!-- Simple Breadcrumb -->
        <div class="mb-3">
            <a href="{{ url('/admin') }}" class="text-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <!-- Header -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h4 class="card-title mb-0">Detail Event</h4>
                <span class="badge badge-primary">{{ $participantCount }} Peserta</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Event Info -->
                    <div class="col-md-12">
                        <h5 class="font-weight-bold text-primary mb-3">{{ $event->title }}</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Tanggal</strong></td>
                                        <td>: {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Event</strong></td>
                                        <td>: {{ $event->type_event ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Penyelenggara</strong></td>
                                        <td>: {{ $event->user->nama_lengkap ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Status</strong></td>
                                        <td>: 
                                            @if(\Carbon\Carbon::parse($event->date)->isPast())
                                                <span class="badge badge-secondary">Selesai</span>
                                            @else
                                                <span class="badge badge-success">Mendatang</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Peserta</strong></td>
                                        <td>: <span class="badge badge-info">{{ $participantCount }} orang</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="font-weight-bold">Deskripsi:</h6>
                            <p class="text-muted">{{ $event->description ?? 'Tidak ada deskripsi untuk event ini.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Participants -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0">Daftar Peserta</h5>
                @if($participantCount > 0)
                    <a href="#" class="btn btn-sm btn-info">
                        <i class="fas fa-file-export"></i> Export Data
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if($participants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="participants-table">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th style="width: 20%">Nama</th>
                                    <th style="width: 20%">Email</th>
                                    <th style="width: 15%">No. Telepon</th>
                                    <th style="width: 15%">Tanggal Bergabung</th>
                                    <th class="text-center" style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $key => $participant)
                                    @php
                                        // Decrypt participant data if encrypted
                                        $userData = $participant->user;
                                        if ($participant->encrypted_data) {
                                            try {
                                                $decryptedData = decrypt($participant->encrypted_data);
                                                $email = $decryptedData['email'] ?? $userData->email ?? 'N/A';
                                                $phone = $decryptedData['phone'] ?? $userData->no_tlp ?? 'N/A';
                                            } catch (\Exception $e) {
                                                $email = $userData->email ?? 'N/A';
                                                $phone = $userData->no_tlp ?? 'N/A';
                                            }
                                        } else {
                                            $email = $userData->email ?? 'N/A';
                                            $phone = $userData->no_tlp ?? 'N/A';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td>{{ $userData->nama_lengkap ?? 'N/A' }}</td>
                                        <td>{{ $email }}</td>
                                        <td>{{ $phone }}</td>
                                        <td>{{ \Carbon\Carbon::parse($participant->created_at)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                @if($participant->certificate_path || isset($decryptedData['certificate_path']))
                                                    <a href="{{ route('admin.download.certificate', ['participant_id' => $participant->id]) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Download Sertifikat">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                @else
                                                    <a href="#" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Generate Sertifikat">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                @endif
                                                <a href="#" class="btn btn-sm btn-danger delete-participant" 
                                                   data-id="{{ $participant->id }}"
                                                   data-toggle="modal" 
                                                   data-target="#deleteParticipantModal" 
                                                   title="Hapus Peserta">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted my-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>Belum ada peserta yang terdaftar untuk event ini.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mb-4">
            <a href="{{ url('/admin') }}" class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>
    
    @include('pointakses.admin.include.sidebar_admin')
</div>

<!-- Delete Participant Modal -->
<div class="modal fade" id="deleteParticipantModal" tabindex="-1" role="dialog" aria-labelledby="deleteParticipantModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteParticipantModalLabel">Konfirmasi Hapus Peserta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus peserta ini dari event?</p>
                <p class="mb-0 text-danger"><small>Tindakan ini tidak dapat dibatalkan dan akan menghapus sertifikat terkait.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteParticipantForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .content-wrapper {
        background-color: #f4f6f9;
        min-height: 100vh;
    }
    .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    .table-borderless td {
        padding: 0.5rem 0.25rem;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.05);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
    }
    .btn-group .btn {
        margin-right: 3px;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#participants-table').DataTable({
            "paging": false,
            "info": false,
            "searching": true,
            "language": {
                "search": "Cari peserta:",
                "zeroRecords": "Tidak ada data peserta yang sesuai",
            }
        });
        
        // Set up delete participant modal
        $('.delete-participant').click(function() {
            const participantId = $(this).data('id');
            $('#deleteParticipantForm').attr('action', '/admin/participant/' + participantId);
        });
    });
</script>
@endpush
@endsection
```