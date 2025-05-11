<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
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

    /**
     * Relasi dengan model Participant
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Relasi dengan model Event melalui pivot participants
     */
    public function participatedEvents()
    {
        return $this->belongsToMany(Event::class, 'participants');
    }

    /**
     * Mengecek apakah pengguna adalah admin
     */
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    /**
     * Mengecek apakah pengguna adalah fakultas
     */
    public function getIsFakultasAttribute()
    {
        return $this->role === 'fakultas';
    }

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}