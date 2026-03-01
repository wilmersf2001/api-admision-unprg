<?php

namespace App\Models;

use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Model;

class UpdateRequest extends Model
{
    use FlexibleQueries;

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

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type'      => 'global_search',
                'column'    => ['unique_code', 'reason', 'note'],
                'relations' => [
                    'postulant' => ['nombres', 'ap_paterno','ap_materno','num_voucher','num_documento'],
                ],
            ],
            'status' => [
                'type'   => 'simple',
                'column' => 'status',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['created_at', 'attended_at', 'status'],
            'default' => 'created_at',
        ];
    }

    public function setReasonAttribute($value)
    {
        $this->attributes['reason'] = strtoupper($value);
    }

    public function setNoteAttribute($value)
    {
        $this->attributes['note'] = strtoupper($value);
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
