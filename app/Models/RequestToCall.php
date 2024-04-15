<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestToCall extends Model
{
    use HasFactory;

    protected $table ='request_to_calls';
    protected $fillable = [
        'name',
        'phone_number',
        'country_code',
        'schedule_time',
        'timezone',
    ];
}
