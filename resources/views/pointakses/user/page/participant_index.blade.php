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
                        <th>Tanda Tangan</th>
                        <th>Logo</th>
                        <th>Aksi</th> <!-- Kolom untuk aksi -->
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
                            <td>
                                <img src="{{ Storage::url($participant->decrypted_signature) }}" alt="Tanda Tangan" style="width: 50px; height: auto;">
                            </td>
                            <td>
                                <img src="{{ Storage::url($participant->decrypted_logo) }}" alt="Logo" style="width: 50px; height: auto;">
                            </td>
                            
                            <td>
                                <!-- Tombol Lihat Detail -->
                                <td>
                                    <!-- Tombol Lihat Detail -->
                                    <a href="{{ route('user.participant.show', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}" 
                                        class="btn btn-primary btn-sm" 
                                        title="Lihat Detail">
                                         <i class="fas fa-eye"></i>
                                     </a>
                                     
                                
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('user.participant.edit', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('user.participant.destroy', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus peserta ini?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                
                                    <!-- Tombol untuk membuat QR Code -->
                                    <a href="{{ route('user.participant.qrcode', ['event_id' => $event_id, 'participant_id' => $participant->id]) }}" 
                                        class="btn btn-info btn-sm" 
                                        title="Buat QR Code">
                                         <i class="fas fa-qrcode"></i>
                                     </a>
                                     
                                </td>
                                


                            </td>
                            
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
