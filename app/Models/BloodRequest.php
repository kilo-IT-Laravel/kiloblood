<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'requester_id',
        'blood_type',
        'status',
        'message',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function requester(){
        return $this->belongsTo(User::class , 'requester_id');
    }

    public function donors(){
        return $this->hasMany(BloodRequestDonor::class);
    }

    public function donations(){
        return $this->hasMany(BloodDonation::class);
    }

    public function hiddenBy(){
        return $this->hasMany(HiddenBloodRequest::class);
    }

    public function scopeVisibleTo($query , $userId){
        return $query->whereDoesntHave('hiddenBy' , function($q) use ($userId){
            $q->where('user_id' , $userId);
        });
    }
}
