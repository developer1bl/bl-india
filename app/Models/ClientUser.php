<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientUser extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_users';
    protected $primaryKey = 'client_users_id';

    protected $fillable = [
        'client_users_name',
        'client_users_img_url',
        'client_users_order',
        'client_users_status'
    ];

    protected $casts = [
        'client_users_status' => 'boolean',
        'client_users_order' => 'integer'
    ];


}
