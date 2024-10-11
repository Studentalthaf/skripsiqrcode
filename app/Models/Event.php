<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';

    // Menentukan kolom yang bisa diisi secara mass-assignment
    protected $fillable = ['user_id', 'title', 'description', 'date', 'type_event', 'logo', 'signature'];  // Ganti 'logo_acara' dengan 'logo'
}

