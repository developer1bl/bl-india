<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use App\Models\Product;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table ='media';
    protected $primaryKey ='media_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
     'media_name',
     'media_type',
     'media_path',
     'media_size',
    ];

    public function service()
    {
        return $this->hasOne(Service::class, 'service_image_id');
    }

    public function product(){

        return $this->hasOne(Product::class, 'service_image_id');
    }

    public function notices(){

        return $this->hasMany(Notice::class,'notice_image_id');
    }
}
