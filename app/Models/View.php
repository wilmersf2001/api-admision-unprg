<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class View extends Model
{
    /** @use HasFactory<\Database\Factories\ViewFactory> */
    use HasFactory,Auditable, FlexibleQueries, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'route',
        'icon',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['name', 'slug', 'route', 'description'],
            ],
            'is_active' => [
                'columns' => ['is_active'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allow' => ['name', 'slug', 'route', 'order', 'created_at'],
            'default' => 'id',
        ];
    }

    /**
     * Relación con el padre (módulo padre)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(View::class, 'parent_id');
    }

    /**
     * Relación con los hijos (submódulos)
     */
    public function children(): HasMany
    {
        return $this->hasMany(View::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Obtiene todos los hijos recursivamente (árbol completo)
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Scope para obtener solo los módulos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para obtener solo los módulos raíz (padres)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Verifica si el view es un módulo raíz
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Verifica si el view tiene hijos
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Relación con permisos
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
