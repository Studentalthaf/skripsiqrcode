@extends('pointakses.admin.layouts.dashboard')
@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="container py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h2 class="m-0 text-primary">
                            <i class="fas fa-calendar-alt mr-2"></i> Dashboard Event
                        </h2>
                        <span class="badge bg-primary">{{ date('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Past Events Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                        <i class="fas fa-history mr-2"></i> History Event
                    </h3>
                    <span class="badge bg-secondary">{{ count($pastEvents) }} events</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jenis</th>
                                <th>Dibuat Oleh</th>
                                <th style="width: 150px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pastEvents as $event)
                            <tr>
                                <td class="font-weight-bold">{{ $event->title }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;"
                                        data-toggle="tooltip" title="{{ $event->description }}">
                                        {{ $event->description }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $event->type_event }}</span>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-user-circle mr-1"></i>
                                        {{ $event->user->nama_lengkap ?? 'Tidak diketahui' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.event.show', ['event' => $event->id]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                    <p>Belum ada event yang terlaksana.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Section -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                        <i class="fas fa-calendar-day mr-2"></i> Event yang Akan Datang
                    </h3>
                    <span class="badge bg-success">{{ count($upcomingEvents) }} events</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jenis</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingEvents as $event)
                            <tr>
                                <td class="font-weight-bold">{{ $event->title }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;"
                                        data-toggle="tooltip" title="{{ $event->description }}">
                                        {{ $event->description }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $event->type_event }}</span>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-user-circle mr-1"></i>
                                        {{ $event->user->nama_lengkap ?? 'Tidak diketahui' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.event.show', ['event' => $event->id]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                    <p>Tidak ada event yang akan datang.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pointakses.admin.include.sidebar_admin')
@endsection

@push('styles')
<style>
    .table {
        margin-bottom: 0;
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.7em;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();

        // Highlight current date events
        const today = new Date().toISOString().split('T')[0];
        $('td:contains(' + today + ')').closest('tr').addClass('bg-light');
    });
</script>
@endpush