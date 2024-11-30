<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonatedBlood extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'donation_id',
        'blood_type',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function donation(){
        return $this->belongsTo(BloodDonation::class , 'donation_id');
    }
}
