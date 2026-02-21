<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'code',
        'title',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected function getFilterConfig(): array
    {
        return [
            'title' => [
                'column' => 'title',
                'operator' => 'LIKE',
            ],
             'code' => [
                'column' => 'code',
                'operator' => 'LIKE',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['code', 'title', 'created_at', 'updated_at'],
            'default' => 'id',
        ];
    }
}
