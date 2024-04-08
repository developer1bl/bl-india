<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Blog;

class BlogCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_categories';
    protected $primaryKey = 'blog_category_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'blog_category_name',
        'blog_category_slug',
        'blog_category_description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_other_details',
        'blog_category_status'
    ];

    protected $casts = [
        'blog_status' => 'boolean'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }
}
