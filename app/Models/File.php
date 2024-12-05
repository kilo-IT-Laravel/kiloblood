<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_url',
        'file_type',
        'file_name'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function bloodRequestDonor()
    {
        return $this->hasOne(BloodRequestDonor::class);
    }

    public function event(){
        return $this->hasOne(Event::class);
    }

    public function banner(){
        return $this->hasOne(banner::class);
    }
}
