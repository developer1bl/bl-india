<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * Interact with the faqs
     */
    protected function productCategoryContent(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    protected $casts = [
        // 'product_category_status' => 'boolean',
        'product_category_order' => 'integer',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class , 'product_product_category', 'product_category_id', 'product_id');
    }

    public function notices()
    {
        return $this->belongsToMany(Notice::class, 'notice_product_categorie', 'product_category_id', 'notice_id')->withPivot('product_id');
    }
}
