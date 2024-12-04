<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentationFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'blood_request_donor_id',
        'file_path',
        'file_type',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodRequestDonor()
    {
        return $this->belongsTo(BloodRequestDonor::class);
    }
}
