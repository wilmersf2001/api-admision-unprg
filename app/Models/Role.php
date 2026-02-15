<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory,Auditable, FlexibleQueries, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function setDescriptionAttribute($value){
        $this->attributes['description'] = strtoupper($value);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['name', 'description'],
            ],
            'is_active' => [
                'type' => 'boolean',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allow' => ['name', 'description', 'created_at'],
            'default' => ['name' => 'asc'],
        ];
    }

     /**
     * Relación con RolePermission
     */
    public function rolePermission()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->wherePivotNull('deleted_at')
            ->whereNull('permissions.deleted_at')
            ->withPivot('granted')
            ->withTimestamps();
    }

        /**
        * Relación con UserRole
        */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
