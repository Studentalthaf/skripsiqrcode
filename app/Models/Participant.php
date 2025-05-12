<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id', 'encrypted_data', 'template_pdf'];

    public function user()
    {
        return $this->belongsTo(User::class)->where('role', 'user');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}