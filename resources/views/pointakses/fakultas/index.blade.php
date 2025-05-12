<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Fakultas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', 'Segoe UI', Roboto, sans-serif;
        }

        .content-wrapper {
            padding: 1.5rem 0;
        }

        .dashboard-title {
            color: #4e73df;
            font-weight: 700;
            margin-bottom: 1.5rem;
            border-left: 4px solid #4e73df;
            padding-left: 15px;
        }

        .stats-card {
            border-radius: 8px;
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .25rem 2.5rem 0 rgba(58, 59, 69, .25);
        }

        .stats-icon {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 3rem;
            opacity: 0.3;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-title {
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .stats-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stats-link {
            color: inherit;
            display: inline-block;
            margin-top: 1rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .stats-link:hover {
            opacity: 0.8;
            transform: translateX(3px);
        }

        .stats-link i {
            margin-left: 5px;
            transition: transform 0.2s ease;
        }

        .stats-link:hover i {
            transform: translateX(3px);
        }

        .blue-card {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }

        .red-card {
            background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
            color: white;
        }

        .events-card {
            border-radius: 8px;
            border: none;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
            margin-bottom: 1.5rem;
        }

        .events-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
        }

        .card-header-title {
            margin: 0;
            font-weight: 700;
            font-size: 1.15rem;
            color: #5a5c69;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.8rem;
            font-weight: 700;
            color: #757575;
        }

        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        .event-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-academic {
            background-color: #4e73df;
            color: white;
        }

        .badge-seminar {
            background-color: #1cc88a;
            color: white;
        }

        .badge-workshop {
            background-color: #f6c23e;
            color: white;
        }

        .badge-other {
            background-color: #858796;
            color: white;
        }

        .empty-state {
            padding: 3rem 0;
            text-align: center;
            color: #858796;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    @extends('pointakses.fakultas.layouts.dashboard')

    @section('content')
    <div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="750">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="dashboard-title">Dashboard Fakultas</h1>
            </div>

            <!-- Stats Cards Row -->
            <div class="row">
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card stats-card blue-card">
                        <div class="card-body">
                            <i class="fas fa-calendar-alt stats-icon"></i>
                            <div class="stats-title">Total Event</div>
                            <div class="stats-value">{{ $eventCount ?? '0' }}</div>
                            <div class="stats-description">Event keseluruhan yang dikelola fakultas</div>
                            <a href="#" class="stats-link">
                                Lihat Detail <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card stats-card red-card">
                        <div class="card-body">
                            <i class="fas fa-calendar-check stats-icon"></i>
                            <div class="stats-title">Event Mendatang</div>
                            <div class="stats-value">{{ isset($upcomingEvents) ? $upcomingEvents->count() : '0' }}</div>
                            <div class="stats-description">Event yang akan berlangsung dalam waktu dekat</div>
                            <a href="#" class="stats-link">
                                Lihat Detail <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Agenda Event -->
            <div class="row mt-1">
                <div class="col-12">
                    <div class="card events-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-header-title">
                                <i class="fas fa-calendar-week me-2"></i>Agenda Event Mendatang
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @if (!isset($upcomingEvents) || $upcomingEvents->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-calendar-xmark"></i>
                                <p>Tidak ada event mendatang saat ini</p>
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th>Nama Event</th>
                                            <th style="width: 130px;">Tanggal</th>
                                            <th style="width: 120px;" class="text-center">Tipe Event</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($upcomingEvents as $key => $event)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="fw-bold">{{ $event->title }}</td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $event->date->format('d M Y') }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                $badgeClass = 'badge-other';
                                                if (strtolower($event->type_event) === 'seminar') {
                                                $badgeClass = 'badge-seminar';
                                                } elseif (strtolower($event->type_event) === 'workshop') {
                                                $badgeClass = 'badge-workshop';
                                                } elseif (strtolower($event->type_event) === 'akademik') {
                                                $badgeClass = 'badge-academic';
                                                }
                                                @endphp
                                                <span class="event-badge {{ $badgeClass }}">
                                                    {{ $event->type_event }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('fakultas.event.show', ['event' => $event->id]) }}"
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
                    </div>
                </div>
            </div>
        </div>

        @include('pointakses.fakultas.include.sidebar_fakultas')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth transitions and interaction effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add subtle animation to stats cards
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
    @endsection
</body>

</html>