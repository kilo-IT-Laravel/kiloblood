<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    use Notifiable, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'message',
        'reference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function readedat(){
        return $this->hasMany(ReadedAt::class);
    }
}
