@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard Monitoring</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Box User -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $userCount ?? '0' }}</h3>
                            <p>User (Mahasiswa)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="{{ route('admin.users') }}" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Box Fakultas -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $fakultasCount ?? '0' }}</h3>
                            <p>User (Fakultas)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <a href="{{ route('admin.users') }}" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Box Total Event -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $eventCount ?? '0' }}</h3>
                            <p>Total Event</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Box Upcoming Event -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ isset($upcomingEvents) ? $upcomingEvents->count() : '0' }}</h3>
                            <p>Event Mendatang</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabel Agenda Event -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Agenda Event Mendatang</h3>
                        </div>
                        <div class="card-body">
                            @if (!isset($upcomingEvents) || $upcomingEvents->isEmpty())
                                <p>Tidak ada event mendatang.</p>
                            @else
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">No</th>
                                            <th>Nama Event</th>
                                            <th>Tanggal</th>
                                            <th>Tipe Event</th>
                                            <th>Pembuat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingEvents as $key => $event)
                                            <tr>
                                                <td>{{ $key + 1 }}.</td>
                                                <td>{{ $event->title }}</td>
                                                <td>{{ $event->date->format('d M Y') }}</td>
                                                <td>{{ $event->type_event }}</td>
                                                <td>{{ $event->user->nama_lengkap ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('pointakses.admin.include.sidebar_admin')
</div>
@endsection
