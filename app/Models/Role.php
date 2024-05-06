<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Permission;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'roles';
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'created_at', 'is_active' , 'role_description'];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     // 'is_active' => 'boolean',
    // ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
