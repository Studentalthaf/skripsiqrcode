<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    // Menentukan kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'type_event',
        'logo',
        'signature',
        'template_pdf',
        'name_x',
        'name_y',
        'placeholders'
    ];
    protected $casts = [
        'placeholders' => 'array',
        'date' => 'date',
        'name_x' => 'integer',
        'name_y' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Participant (Event memiliki banyak Participant)
    public function participants()
    {
        return $this->hasMany(Participant::class);  // Relasi satu ke banyak
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'participants');
    }
}
