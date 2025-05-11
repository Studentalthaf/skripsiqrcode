@extends('pointakses.fakultas.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <h3 class="font-weight-bold">Daftar Acara</h3>
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/fakultas') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Acara</li>
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
                <h3 class="card-title">Acara</h3>
                <a href="{{ route('fakultas.create.event') }}" class="btn btn-success btn-sm">Tambah Acara</a>
            </div>
            <div class="card-body p-0">
                @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Tipe Acara</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->date }}</td>
                                <td>{{ $event->type_event }}</td>
                                <td>
                                    <a href="{{ route('fakultas.index.participant', ['event_id' => $event->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        Data Peserta
                                    </a>
                                    @if($event->template_pdf)
                                    <a href="{{ route('fakultas.edit.placeholder', ['event_id' => $event->id]) }}"
                                        class="btn btn-info btn-sm">
                                        Atur Placeholder
                                    </a>
                                    @endif
                                    <a href="{{ route('fakultas.event.edit', $event->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('fakultas.event.delete', $event->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus acara ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="p-3">Belum ada acara yang dibuat.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@include('pointakses.fakultas.include.sidebar_fakultas')
@endsection