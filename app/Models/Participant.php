<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    // Field yang dapat diisi (mass assignable)
    protected $fillable = [
        'event_id', 
        'encrypted_data', // Data terenkripsi peserta
    ];

    // Relasi ke model Event (many-to-one)
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
