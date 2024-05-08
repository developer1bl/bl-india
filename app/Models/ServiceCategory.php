<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='service_categories';
    protected $fillable = [
        'service_category_name',
        'service_category_slug',
        'category_img_url',
        'category_img_alt',
        'category_status'
    ];

    public function services(){
        return $this->hasMany(Service::class,'service_category_id','id');
    }

}
