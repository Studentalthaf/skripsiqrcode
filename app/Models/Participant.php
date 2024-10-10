<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari default (optional)
    // protected $table = 'participants';

    // Kolom yang boleh diisi secara massal (mass-assignable)
    protected $fillable = [
        'event_id',
        'nama_peserta',
        'instansi',
        'signature',
        'logo',
        'serial_number',
        'qrcode',
        'key', // disimpan, tetapi tidak ditampilkan
        'nonce', // disimpan, tetapi tidak ditampilkan
    ];

    // Model Participant
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
