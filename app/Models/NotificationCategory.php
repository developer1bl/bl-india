<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notification_categories';
    protected $primaryKey = 'notification_category_id';

    protected $fillable = [
        'notification_category_name',
        'notification_category_slug',
        'notification_category_type',
        'notification_category_status',
    ];

    // Define the relationship with Notice if needed
    public function notices()
    {
        return $this->hasMany(Notice::class, 'notification_category_id', 'notification_category_id');
    }
}
