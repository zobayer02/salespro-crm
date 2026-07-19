<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Customer extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class);
    }

    public function reengagementLogs(): HasMany
    {
        return $this->hasMany(ReengagementLog::class);
    }

    public function scopeWithPurchaseMetrics(Builder $query): Builder
    {
        return $query
            ->withCount('sales')
            ->withSum('sales as total_spent', 'total_amount')
            ->withMax('sales as last_purchase_at', 'sold_at');
    }

    public function scopeLost(Builder $query, ?int $days = null): Builder
    {
        $cutoff = now()->subDays($days ?? self::lostCustomerDays());

        return $query->where(function (Builder $query) use ($cutoff): void {
            $query->whereDoesntHave('sales')
                ->orWhereRaw('(select max(sold_at) from sales where sales.customer_id = customers.id) < ?', [$cutoff]);
        });
    }

    public function scopeActiveByPurchaseHistory(Builder $query, ?int $days = null): Builder
    {
        $cutoff = now()->subDays($days ?? self::lostCustomerDays());

        return $query->whereRaw('(select max(sold_at) from sales where sales.customer_id = customers.id) >= ?', [$cutoff]);
    }

    public function lastPurchaseAt(): ?Carbon
    {
        $lastPurchaseAt = $this->last_purchase_at ?? $this->sales()->max('sold_at');

        return $lastPurchaseAt ? Carbon::parse($lastPurchaseAt) : null;
    }

    public function isLostCustomer(?int $days = null): bool
    {
        $lastPurchaseAt = $this->lastPurchaseAt();

        return $lastPurchaseAt === null || $lastPurchaseAt->lt(now()->subDays($days ?? self::lostCustomerDays()));
    }

    public static function lostCustomerDays(): int
    {
        return max((int) config('salespro.lost_customer_days', 90), 1);
    }
}
