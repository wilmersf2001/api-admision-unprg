<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Model
{
    /** @use HasFactory<\Database\Factories\RolePermissionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'permission_id',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Relación con el rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relación con el permiso
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
