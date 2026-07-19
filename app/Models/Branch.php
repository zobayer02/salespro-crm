<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'code',
        'address',
        'status',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(BranchInventory::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
