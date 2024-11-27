<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendOTP extends Model
{
    protected $table = 'send_otps';

    protected $fillable = [
        'email',
        'otp',
        'created_at'
    ];
}
