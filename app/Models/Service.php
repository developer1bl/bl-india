<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\ServiceSection;

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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_services', 'service_id', 'product_id')
                    ->withPivot('service_type', 'service_compliance');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'service_id', 'service_id');
    }

    public function image()
    {
        return $this->belongsTo(Media::class, 'service_image_id');
    }

    public function service_section()
    {
        return $this->hasMany(ServiceSection::class,'service_id','service_id');
    }

    public function notices_product()
    {
        return $this->belongsToMany(Notice::class, 'notice_service', 'service_id', 'notice_id')
                    ->withPivot('product_id');
    }
}
