<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'galleries';
    protected $primaryKey = 'gallery_id';

    protected $fillable = [
        'gallery_image_title',
        'gallery_image_slug',
        'media_Url',
        'img_alt',
    ];
}
