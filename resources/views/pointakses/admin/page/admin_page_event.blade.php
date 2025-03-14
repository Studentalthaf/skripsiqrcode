@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 d-flex justify-content-between align-items-center">
                <div class="col-12 col-md text-center text-md-left">
                    <h3 class="font-weight-bold">Acara</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Acara</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header d-flex flex-column align-items-start">
                    <h3 class="card-title mb-2">Daftar Acara</h3>
                    <a href="{{ route('admin.create.event') }}" class="btn btn-success btn-sm">
                        Tambah Acara
                    </a>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped text-nowrap">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Type Acara</th>
                                <th>Deskripsi Acara</th>
                                <th colspan="2" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $key => $event)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->date }}</td>
                                    <td>{{ $event->type_event }}</td>
                                    <td>{{ $event->description }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.event.edit', $event->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('admin.event.delete', $event->id) }}" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus acara ini?')">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('user.participant.index', $event->id) }}" class="btn btn-primary btn-sm">
                                            Data Peserta
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pointakses.admin.include.sidebar_admin')
@endsection
