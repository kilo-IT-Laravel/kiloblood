<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'donor_id', 
        'status', 
        'blood_type', 
        'name', 
        'location', 
        'quantity', 
        'note', 
        'expired_at', 
        'medical_records'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function requester(){
        return $this->belongsTo(User::class , 'donor_id');
    }

    public function donors(){
        return $this->hasMany(BloodRequestDonor::class);
    }
}
