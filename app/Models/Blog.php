<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlogCategory;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';
    protected $primaryKey = 'blog_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'blog_title',
        'blog_slug',
        'blog_category_id',
        'blog_image_id',
        'blog_img_alt',
        'blog_content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'blog_status',
        'seo_other_details',
        'blog_tags',
    ];

    protected $casts = [
        'blog_status' => 'boolean',
        'blog_tags' => 'array',
    ];

    public function blogcategory()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
