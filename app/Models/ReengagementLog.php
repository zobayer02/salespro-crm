<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReengagementLog extends Model
{
    use HasFactory;

    public const CHANNEL_EMAIL = 'email';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'customer_id',
        'customer_assignment_id',
        'channel',
        'status',
        'subject',
        'message',
        'failure_reason',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CustomerAssignment::class, 'customer_assignment_id');
    }
}
