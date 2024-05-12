<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;
use App\Models\ServiceSection;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'services';
    protected $primaryKey = "service_id";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'service_name',
        'service_slug',
        'service_category_id',
        'service_img_url',
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
    ];

    protected $casts = [
        'service_order' => 'integer',
        'service_compliance' => 'json',
        'faqs' => 'json',
    ];

    /**
     * Interact with service_compliance
     */
    protected function serviceCompliance(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with the faqs
     */
    protected function faqs(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    /**
     * Interact with the faqs
     */
    protected function serviceDescription(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => json_encode($value),
        );
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_services', 'service_id', 'product_id')
                     ->withPivot('service_type', 'service_compliance');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'service_id', 'service_id');
    }

    public function service_section()
    {
        return $this->hasMany(ServiceSection::class, 'service_id', 'service_id');
    }

    public function notices_product()
    {
        return $this->belongsToMany(Notice::class, 'notice_service', 'service_id', 'notice_id')
            ->withPivot('product_id');
    }

    public function service_category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id', 'id');
    }

    // Define the attributes and relationships
    public function scopeLatestService($query, $limit = 4)
    {
        return $query->orderBy('created_at', 'desc')->take($limit)->get();
    }
}
