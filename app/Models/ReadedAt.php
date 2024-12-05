<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadedAt extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function notification(){
        return $this->belongsTo(Notification::class);
    }
}
