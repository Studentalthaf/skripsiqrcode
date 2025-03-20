@extends('pointakses.admin.layouts.dashboard')

@section('content')
<div class="container">
    <div class="certificate" style="text-align: center; border: 5px solid #4CAF50; border-radius: 10px; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-width: 600px; margin: auto;">
        <h1 style="color: #4CAF50; font-size: 2.5em; margin-bottom: 0;">SERTIFIKAT</h1>
        
        <img src="{{ asset('storage/' . $participant->decrypted_logo) }}" alt="Signature" style="width: 100px; height: auto;" />
        <h2>Ini adalah untuk menyatakan bahwa</h2>
        <p><strong>{{ $participant->decrypted_name }}</strong></p>
        <p>{{ $participant->decrypted_email }}</p>
        <p>{{ $participant->decrypted_phone }}</p>
        <p>{{  $participant->decrypted_nomer_seri}}</p>
        <p>telah menyelesaikan program</p>
        <h2>{{  $participant->decrypted_title }}</h2>
        <p>yang diselenggarakan pada</p>
        <p><strong> {{ $participant->decrypted_date }}</strong></p>

        <div class="signature" style="margin-top: 40px; font-size: 1em; text-align: right;">
            <p>{{  $participant->decrypted_nama_lengkap }}</p>
            <p>Panitia Penyelenggara</p>
            <p><strong>Logo:</strong></p>
            <img src="{{ asset('storage/' . $participant->decrypted_signature) }}" alt="Logo" style="width: 100px; height: auto;" />
        </div>
        <div class="date" style="font-size: 0.9em; margin-top: 10px;">
            <p>{{ $participant->decrypted_date }}</p>
        </div>
    </div>
</div>
@endsection