<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = "product_id";
    protected $dates = ['deleted_at'];


    protected $fillable = [
        'product_name',
        'product_slug',
        'product_image_id',
        'product_img_alt',
        'product_compliance',
        'product_content',
        'product_service_id',
        'product_category_id',
        'information',
        'guidelines',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'product_status',
        'product_order',
    ];

}
