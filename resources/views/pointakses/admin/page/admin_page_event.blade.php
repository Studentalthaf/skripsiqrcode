```blade
@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <!-- Content Header -->
    <section class="content-header bg-light shadow-sm py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 text-center text-md-left mb-2 mb-md-0">
                    <h3 class="font-weight-bold text-dark mb-0">Manajemen Acara</h3>
                    <small class="text-muted">Kelola semua acara dengan mudah</small>
                </div>
                <div class="col-12 col-md-6 text-center text-md-right">
                    <ol class="breadcrumb float-md-right bg-transparent mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}" class="text-primary">Dashboard</a></li>
                        <li class="breadcrumb-item active">Acara</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h3 class="card-title mb-0">Daftar Acara</h3>
                            <a href="{{ route('admin.create.event') }}" class="btn btn-success btn-sm px-3">
                                <i class="fas fa-plus mr-1"></i> Tambah Acara
                            </a>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-nowrap mb-0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%">#</th>
                                            <th class="align-middle" style="width: 20%">Nama Event</th>
                                            <th class="align-middle" style="width: 15%">Tanggal</th>
                                            <th class="align-middle" style="width: 15%">Tipe Acara</th>
                                            <th class="align-middle" style="width: 25%">Deskripsi</th>
                                            <th class="text-center align-middle" style="width: 10%">Placeholder</th>
                                            <th class="text-center align-middle" style="width: 20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($events as $key => $event)
                                            <tr>
                                                <td class="text-center align-middle">{{ $events->firstItem() + $key }}</td>
                                                <td class="align-middle">{{ $event->title }}</td>
                                                <td class="align-middle">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                                                <td class="align-middle">{{ $event->type_event ?? 'N/A' }}</td>
                                                <td class="align-middle">{{ Str::limit($event->description, 50, '...') }}</td>
                                                <td class="text-center align-middle">
                                                    <a href="{{ route('admin.event.placeholder', ['id' => $event->id]) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-cog"></i> Set
                                                    </a>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.event.edit', $event->id) }}" class="btn btn-warning btn-sm mr-1" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('admin.event.delete', $event->id) }}" class="btn btn-danger btn-sm mr-1" title="Hapus"
                                                           onclick="return confirm('Yakin ingin menghapus acara ini?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                        <a href="{{ route('admin.index.participant', $event->id) }}" class="btn btn-info btn-sm" title="Peserta">
                                                            <i class="fas fa-users"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                                                    Belum ada acara yang tersedia.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2">
                                <div class="text-muted">
                                    Menampilkan {{ $events->firstItem() }} - {{ $events->lastItem() }} dari {{ $events->total() }} data
                                </div>
                                <div>
                                    {{ $events->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('pointakses.admin.include.sidebar_admin')
@endsection

@section('styles')
<style>
    .content-wrapper {
        background-color: #f4f6f9;
        min-height: 100vh;
    }
    .content-header {
        border-bottom: 1px solid #e5e5e5;
    }
    .card {
        border-radius: 0.5rem;
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .card-header {
        border-bottom: 1px solid #e5e5e5;
    }
    .table {
        margin-bottom: 0;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 0.75rem;
    }
    .table thead th {
        border-bottom: none;
        font-weight: 600;
    }
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        transition: background-color 0.2s ease;
    }
    .btn-sm i {
        font-size: 0.875rem;
    }
    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }
    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
    }
    .btn-warning {
        background-color: #ffca2c;
        border-color: #ffca2c;
        color: #212529;
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
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
    .breadcrumb-item a {
        text-decoration: none;
        color: #007bff;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }
    .page-item .page-link {
        color: #007bff;
        border-radius: 0.25rem;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
    }
    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }
    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }
    .page-link:hover:not(.disabled) {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #0056b3;
    }
</style>
@endsection
```