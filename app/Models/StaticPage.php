<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaticPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'static_page_id'; // Specify the custom primary key field name
    protected $table = 'static_pages';

    protected $fillable = [
        'page_name',
        'page_slug',
        'tagline',
        'page_img_url',
        'page_image_alt',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'page_status'
    ];

    protected $casts = [
        // 'page_status' => 'boolean'
    ];

    public function pageSection(){
        return $this->hasMany(StaticPageSection::class,'static_page_id','static_page_id');
    }
}
