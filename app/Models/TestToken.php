<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestToken extends Model
{
    protected $fillable = ['token' , 'is_used' , 'user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
