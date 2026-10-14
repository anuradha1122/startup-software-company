<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'supplier_number',
        'name',
        'legal_name',
        'email',
        'phone',
        'website',
        'tax_number',
        'address',
        'city',
        'country',
        'default_currency',
        'status',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Supplier $supplier
        ): void {
            if (! $supplier->public_id) {
                $supplier->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'default_currency' => 'string',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(
            ProjectExpense::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('suppliers')
            ->logOnly([
                'supplier_number',
                'name',
                'email',
                'phone',
                'status',
                'default_currency',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
