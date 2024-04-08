<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FormData;

class CustomForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'custom_forms';
    protected $primaryKey = 'custom_form_id';

    protected $fillable = [
        'form_name',
        'form_slug',
        'form_email',
        'form_components',
        'form_status',
    ];

    protected $casts = [
        'form_components' => 'json', // Assuming form_components is stored as JSON in the database
        'form_status' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function formData()
    {
        return $this->hasMany(FormData::class, 'form_id', 'custom_form_id');
    }
}
