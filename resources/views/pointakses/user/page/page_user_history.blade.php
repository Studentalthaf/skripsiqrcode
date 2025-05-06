@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Riwayat Acara yang Diikuti</h3>
            </div>
            <div class="card-body">
                @if($events->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->date->format('d-m-Y') }}</td>
                                    <td>{{ $event->type_event }}</td>
                                    <td>{{ $event->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Tidak ada acara yang pernah diikuti.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@include('pointakses.user.include.sidebar_user')
@endsection
