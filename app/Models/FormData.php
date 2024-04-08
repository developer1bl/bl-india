<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CustomForm;

class FormData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_data';
    protected $primaryKey = 'form_data_id';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'form_id',
        'form_data',
        'form_data_response',
        'form_data_status',
    ];

    protected $casts = [
        'form_data' => 'json', // Assuming form data is stored as JSON in the database
        'form_data_status' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'form_id', 'custom_form_id');
    }
}
