<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    public $table ='services';
    protected $primaryKey = "service_id";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'service_name',
        'service_slug',
        'service_image_id',
        'service_img_alt',
        'service_compliance',
        'service_description',
        'faqs',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'service_order',
        'service_status',
        'service_featured',
        'service_product_show'
    ];

    protected $casts = [
        'service_status' => 'boolean',
        'service_featured' => 'boolean',
        'service_product_show' => 'boolean',
        'service_order' => 'integer',
        'faqs' => 'array'
    ];
}
