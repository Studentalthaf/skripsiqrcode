@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-4 align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Daftar Acara</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Acara</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            {{-- Flash Messages --}}
            <div class="row">
                <div class="col-12">
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
                </div>
            </div>

            {{-- Events Card --}}
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt mr-2"></i>Daftar Acara
                                </h3>
                                <a href="{{ route('fakultas.create.event') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i>Tambah Acara Baru
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($events->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" style="width: 50px;">#</th>
                                                <th>Judul Acara</th>
                                                <th>Tanggal</th>
                                                <th>Tipe Acara</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($events as $event)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $event->title }}</td>
                                                <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                                                <td>
                                                    <span class="badge 
                                                        @if($event->type_event == 'Seminar') badge-info 
                                                        @elseif($event->type_event == 'Workshop') badge-success 
                                                        @else badge-secondary 
                                                        @endif">
                                                        {{ $event->type_event }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('fakultas.index.participant', ['event_id' => $event->id]) }}" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Data Peserta">
                                                            <i class="fas fa-users"></i>
                                                        </a>
                                                        
                                                        @if($event->template_pdf)
                                                        <a href="{{ route('fakultas.edit.placeholder', ['event_id' => $event->id]) }}" 
                                                           class="btn btn-warning btn-sm" 
                                                           title="Atur Placeholder">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        <a href="{{ route('fakultas.event.edit', $event->id) }}" 
                                                           class="btn btn-primary btn-sm" 
                                                           title="Edit Acara">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('fakultas.event.delete', $event->id) }}"
                                                              method="POST" 
                                                              style="display:inline;"
                                                              class="delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-danger btn-sm delete-btn" 
                                                                    title="Hapus Acara"
                                                                    onclick="return confirm('Yakin ingin menghapus acara ini?')">
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
                                <div class="text-center p-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada acara yang dibuat.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function(e) {
            const form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Anda yakin ingin menghapus acara ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush