<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory,Auditable, FlexibleQueries, SoftDeletes;

    protected $fillable = [
        'view_id',
        'code',
        'name',
        'description',
        'module',
        'policy_method',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['code', 'name', 'description', 'module'],
            ],
            'is_active' => [
                'type' => 'boolean',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allow' => ['code', 'name', 'module', 'created_at'],
            'default' => ['name' => 'asc'],
        ];
    }

    public function view()
    {
        return $this->belongsTo(View::class);
    }
}
