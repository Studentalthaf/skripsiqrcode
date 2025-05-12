<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama_lengkap',
        'NIM',
        'email',
        'password',
        'no_tlp',
        'unit_kerja',
        'role',
        'alamat',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function participatedEvents()
    {
        return $this->belongsToMany(Event::class, 'participants');
    }

    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function getIsFakultasAttribute()
    {
        return $this->role === 'fakultas';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}