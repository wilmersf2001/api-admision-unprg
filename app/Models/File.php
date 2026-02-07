<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes;

    protected $table = 'files';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'name',
        'original_name',
        'type',
        'type_entitie',
        'path',
        'disk',
        'is_public',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'status' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
