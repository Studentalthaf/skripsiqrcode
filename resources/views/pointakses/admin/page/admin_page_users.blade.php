@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
<div class="container mt-3">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Semua User</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="width: 40px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $user)
                    <tr>
                        <td>{{ $key + 1 }}.</td>
                        <td>{{ $user->nama_lengkap }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->role == 'admin')
                                <span class="badge bg-danger">{{ ucfirst($user->role) }}</span>
                            @elseif ($user->role == 'operator')
                                <span class="badge bg-warning">{{ ucfirst($user->role) }}</span>
                            @elseif ($user->role == 'wali')
                                <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-primary">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>
@include('pointakses.admin.include.sidebar_admin')
@endsection
