<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAssignment extends Model
{
    use HasFactory;

    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'customer_id',
        'employee_id',
        'status',
        'assigned_at',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reengagementLogs(): HasMany
    {
        return $this->hasMany(ReengagementLog::class);
    }
}
