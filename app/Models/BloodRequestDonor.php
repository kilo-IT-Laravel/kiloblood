<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequestDonor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blood_request_id',
        'requester_id',
        'status',
        'quantity',
        'is_confirmed',
        'confirmed_quantity'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'confirmed_quantity' => 'integer'

    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }



}
