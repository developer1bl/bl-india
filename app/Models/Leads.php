<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leads extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';
    protected $primaryKey = 'lead_id';
    protected $dates = ['expires_at', 'created_at', 'updated_at', 'deleted_at'];
    
    protected $fillable = [
        'name',
        'email',
        'country',
        'phone',
        'service',
        'message',
        'status',
        'source',
        'ip_address',
        'pdf_path',
        'expires_at',
        'organization',
    ];
}
