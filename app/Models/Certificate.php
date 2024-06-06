<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificates';
    protected $primaryKey = 'certificate_id';

    protected $fillable = [
        'certificates_name',
        'certificates_slug',
        'certificates_img_url'
    ];
}
