<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='social_media';
    protected $primaryKey ='social_media_id';

    protected $fillable = [
        'social_media_name',
        'social_link_url',
        'social_icon_url',
        'social_link_status'
    ];
}
