<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerForm extends Model
{
    use HasFactory;

    protected $table = 'partner_forms';

    protected $fillable = [
        'user_name',
        'org_name',
        'designation',
        'email',
        'phone',
        'country_code',
        'location',
        'website',
        'user_message',
        'status'
    ];

    protected $casts = [
        'status' => 'integer'
    ];
}
