@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="content">
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <!-- Tombol untuk menambahkan acara -->
                    <a href="{{ route('user.acara.tambah') }}" class="btn btn-success" style="font-size: 12px; padding: 5px 5px; width: 100px;">Tambah acara</a>

                </div>

                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Type Acara</th>
                                <th>Deskripsi Acara</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $key => $event)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->date }}</td>
                                    <td>{{ $event->type_event}}}</td>
                                    <td>{{ $event->description }}</td> 
                                    <td>
                                        <!-- Tombol Hapus -->
                                        <a href="{{ route('user.acara.hapus', $event->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus acara ini?')">Hapus</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

@include('pointakses.user.include.sidebar_user')
@endsection
