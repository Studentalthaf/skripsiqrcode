@extends('pointakses.admin.layouts.dashboard')
@section('content')
<div class="container">
    <div class="certificate" style="border: 5px solid #3498db; border-radius: 10px; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); max-width: 800px; margin: auto;">
        <!-- Header with Title -->
        <h1 style="color: #3498db; font-size: 2.5em; text-align: center; margin-bottom: 20px;">SERTIFIKAT</h1>
        
        <!-- Logo and Signature area -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div style="text-align: center; width: 40%;">
                <img src="{{ asset('storage/' . $participant->decrypted_logo) }}" alt="Logo" style="width: 120px; height: auto;" />
                <p style="margin-top: 5px; font-weight: bold;">Logo</p>
            </div>
            <div style="text-align: center; width: 40%;">
                <img src="{{ asset('storage/' . $participant->decrypted_signature) }}" alt="Signature" style="width: 120px; height: auto;" />
                <p style="margin-top: 5px; font-weight: bold;">Tanda Tangan</p>
            </div>
        </div>
        
        <!-- Introduction text -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>Data ini untuk menyatakan bahwa</h2>
        </div>
        
        <!-- Data Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; width: 30%; font-weight: bold; background-color: #e8f6fe;">Nama</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Email</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_email }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Telepon</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_phone }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Nomor Seri</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_nomer_seri }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Program</td>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">{{ $participant->decrypted_title }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Tanggal</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_date }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold; background-color: #f2f2f2;">Penyelenggara</td>
                <td style="padding: 10px; border: 1px solid #ddd;">{{ $participant->decrypted_nama_lengkap }}</td>
            </tr>
        </table>
        
        <!-- Footer text -->
        <div style="text-align: center; font-style: italic; margin-top: 20px;">
            <p>Sertifikat ini diberikan sebagai penghargaan atas keberhasilan menyelesaikan program.</p>
            <p style="margin-top: 10px; font-weight: bold;">{{ $participant->decrypted_date }}</p>
        </div>
    </div>
</div>

@endsection