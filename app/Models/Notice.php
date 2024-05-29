<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use App\Models\Document;
use App\Models\ProductCategories;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notices';
    protected $primaryKey = 'notice_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'notice_title',
        'notice_slug',
        'notice_img_url',
        'notice_img_alt',
        'notice_content',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'notice_doc_url',
        'notice_status',
        'products_tag',
        'seo_other_details',
    ];

    protected $casts = [
        // 'notice_status' => 'boolean',
        'products_tag' => 'json',
        'notice_image_id' => 'integer',
        'notice_document_id' => 'integer',
        'notice_order' => 'integer',
    ];

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategories::class, 'notice_product_categorie', 'notice_id', 'product_category_id')
                    ->withPivot('product_id');
    }

    public function notice_service()
    {
        return $this->belongsToMany(Service::class, 'notice_service', 'notice_id', 'service_id')
                    ->withPivot('product_id');
    }
}
