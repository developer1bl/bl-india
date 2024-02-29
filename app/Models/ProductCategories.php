<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategories extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'product_categories';
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'product_category_id';

    protected $fillable = [
        'product_category_name',
        'product_category_slug',
        'product_category_content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'product_category_status',
        'product_category_order',
    ];

    protected $casts = [
        'product_category_status' => 'boolean',
        'product_category_order' => 'integer',
    ];
}
