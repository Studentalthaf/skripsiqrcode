@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
    <!-- Header Dashboard -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card bg-gradient-primary text-white shadow-lg">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="m-0 display-5 font-weight-bold">
                                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Monitoring
                                    </h1>
                                    <p class="mb-0 mt-2 text-white-50">
                                        <i class="far fa-calendar-alt mr-1"></i> {{ now()->format('l, d F Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="h3 mb-0">Administrator</div>
                                    <span class="badge badge-light">
                                        <i class="fas fa-user-shield mr-1"></i> Online
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistik Cards -->
            <div class="row">
                <!-- Box User -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="info-box shadow-sm bg-white">
                        <span class="info-box-icon bg-info elevation-1">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">User (Mahasiswa)</span>
                            <span class="info-box-number display-6">{{ $userCount ?? '0' }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 100%"></div>
                            </div>
                            <a href="{{ route('admin.users') }}" class="small-box-footer mt-2 d-block text-info">
                                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Box Fakultas -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="info-box shadow-sm bg-white">
                        <span class="info-box-icon bg-success elevation-1">
                            <i class="fas fa-university"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">User (Fakultas)</span>
                            <span class="info-box-number display-6">{{ $fakultasCount ?? '0' }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <a href="{{ route('admin.users') }}" class="small-box-footer mt-2 d-block text-success">
                                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Box Total Event -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="info-box shadow-sm bg-white">
                        <span class="info-box-icon bg-warning elevation-1">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Event</span>
                            <span class="info-box-number display-6">{{ $eventCount ?? '0' }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 100%"></div>
                            </div>
                            <a href="{{route('admin.event.history')}}" class="small-box-footer mt-2 d-block text-warning">
                                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Box Upcoming Event -->
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="info-box shadow-sm bg-white">
                        <span class="info-box-icon bg-danger elevation-1">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Event Mendatang</span>
                            <span class="info-box-number display-6">{{ isset($upcomingEvents) ? $upcomingEvents->count() : '0' }}</span>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                            <a href="#upcoming-events" class="small-box-footer mt-2 d-block text-danger">
                                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Agenda Event -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title m-0">
                                    <i class="fas fa-calendar-week mr-2"></i> Agenda Event Mendatang
                                </h3>
                                <div>
                                    <span class="badge badge-success">
                                        <i class="fas fa-calendar-day mr-1"></i> 
                                        {{ isset($upcomingEvents) ? $upcomingEvents->count() : '0' }} Event
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" id="upcoming-events">
                            @if (!isset($upcomingEvents) || $upcomingEvents->isEmpty())
                            <div class="text-center py-5">
                                <img src="{{ asset('img/no-data.svg') }}" alt="Tidak ada event" style="max-width: 200px; opacity: 0.5;" onerror="this.src='https://via.placeholder.com/200x150?text=Tidak+Ada+Event'">
                                <h5 class="text-muted mt-3">Tidak ada event mendatang.</h5>
                                <p class="text-muted">Event yang akan datang akan ditampilkan di sini</p>
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px" class="text-center">No</th>
                                            <th>Nama Event</th>
                                            <th>Tanggal</th>
                                            <th>Tipe Event</th>
                                            <th>Pembuat</th>
                                            <th style="width: 150px" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingEvents as $key => $event)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}.</td>
                                            <td>
                                                <span class="font-weight-bold">{{ $event->title }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ $event->date->format('d M Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $event->type_event }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-light text-primary mr-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; border-radius: 50%;">
                                                        <i class="fas fa-user-circle"></i>
                                                    </div>
                                                    {{ $event->user->nama_lengkap ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.event.show', ['event' => $event->id]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white text-center">
                            <a href="{{ route('admin.event.history') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list mr-1"></i> Lihat Semua Event
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('pointakses.admin.include.sidebar_admin')
</div>

@push('styles')
<style>
    .info-box {
        border-radius: 0.5rem;
        min-height: 120px;
        transition: all 0.3s ease;
    }
    
    .info-box:hover {
        transform: translateY(-5px);
    }
    
    .info-box-icon {
        border-radius: 0.5rem 0 0 0.5rem;
        width: 80px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .info-box-icon i {
        font-size: 2rem;
    }
    
    .info-box-content {
        padding: 15px;
    }
    
    .progress {
        height: 5px;
        margin-top: 10px;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }
    
    .badge {
        padding: 0.4em 0.7em;
        font-weight: 500;
    }
    
    .avatar {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(to right, #4a89dc, #5e72e4);
    }
    
    .display-6 {
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    @media (max-width: 767.98px) {
        .info-box {
            min-height: auto;
        }
        
        .info-box-icon {
            width: 60px;
        }
        
        .info-box-icon i {
            font-size: 1.5rem;
        }
        
        .display-6 {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(function() {
        // Animasi counting untuk angka statistik
        $('.info-box-number').each(function() {
            const $this = $(this);
            const countTo = parseInt($this.text().trim(), 10);
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 1000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
        
        // Highlight row saat hover
        $('.table-hover tbody tr').hover(
            function() {
                $(this).addClass('bg-light-hover');
            },
            function() {
                $(this).removeClass('bg-light-hover');
            }
        );
        
        // Sorot event yang terdekat
        const today = new Date().toISOString().split('T')[0];
        // Mencari event terdekat (setelah hari ini) dan memberikan highlight khusus
        let closestFutureEvent = null;
        $('.table tbody tr').each(function() {
            const eventDate = $(this).find('td:nth-child(3)').text().trim();
            if (eventDate >= today && (closestFutureEvent === null || eventDate < closestFutureEvent)) {
                closestFutureEvent = eventDate;
                $(this).addClass('bg-light-warning');
            }
        });
    });
</script>
@endpush
@endsection