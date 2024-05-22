<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductCategories;
use App\Models\Service;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = "product_id";
    protected $dates = ['deleted_at'];


    protected $fillable = [
        'product_name',
        'product_slug',
        'product_img_url',
        'product_technical_name',
        'product_img_alt',
        'product_compliance',
        'product_content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'product_status',
        'product_order',
    ];

    protected $casts = [
        // 'product_status' => 'boolean',
        'product_order' => 'integer',
        'product_content' => 'json',
    ];

     /**
     * Interact with the faqs
     */
    protected function productContent(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategories::class, 'product_product_category', 'product_id', 'product_category_id');
    }

    public function productService()
    {
        return $this->belongsToMany(Service::class, 'product_services', 'product_id', 'service_id')
                     ->withPivot('service_type', 'service_compliance');
    }
}
