<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'designation',
        'kpi_score',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'kpi_score' => 'integer',
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class);
    }
}
