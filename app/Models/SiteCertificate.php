<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='site_certificates';

    protected $fillable = [
        'site_certificate_name',
        'site_certificate_slug',
        'site_certificate_url',
        'site_certificate_status'
    ];
}
