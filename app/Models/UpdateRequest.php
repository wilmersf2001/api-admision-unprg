<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateRequest extends Model
{
    protected $table = 'tb_update_requests';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'postulant_id',
        'reviewed_by',
        'status',
        'reason',
        'note',
        'unique_code',
        'code_used',
        'code_expires_at',
        'old_values',
        'new_values',
        'attended_at',
    ];

    protected function casts(): array
    {
        return [
            'code_used'   => 'boolean',
            'code_expires_at' => 'datetime',
            'attended_at' => 'datetime',
            'old_values'  => 'array',
            'new_values'  => 'array',
        ];
    }

    public function postulant()
    {
        return $this->belongsTo(Postulant::class, 'postulant_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isExpired(): bool
    {
        return $this->code_expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return !$this->code_used && !$this->isExpired() && $this->status === self::STATUS_PENDING;
    }
}