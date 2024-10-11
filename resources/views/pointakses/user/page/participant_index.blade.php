@extends('pointakses.user.layouts.dashboard')

@section('content')
<div class="content-wrapper iframe-mode" data-widget="iframe" data-loading-screen="600">
    <div class="container">
        <h2>Semua Peserta</h2>

        <!-- Tombol untuk menambah peserta -->
        <a href="{{ route('user.participant.create', ['event_id' => $event_id]) }}" class="btn btn-success mb-3"
           style="font-size: 12px; padding: 5px 10px; width: 150px;">
            Tambah Peserta
        </a>

        <!-- Jika ada peserta -->
        @if($participants->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Email</th>
                        <th>Telepon</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <!-- Menampilkan data yang sudah didekripsi -->
                            <td>{{ $participant->decrypted_name }}</td>
                            <td>{{ $participant->decrypted_email }}</td>
                            <td>{{ $participant->decrypted_phone }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Belum ada peserta yang terdaftar.</p>
        @endif
    </div>
    @include('pointakses.user.include.sidebar_user')
</div>
@endsection
