<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReportRun extends Model
{
    protected $fillable = [
        'report_type',
        'format',
        'status',
        'filters',
        'download_name',
        'row_count',
        'failure_message',
        'generated_by',
        'generated_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ReportRun $reportRun
        ): void {
            if (! $reportRun->public_id) {
                $reportRun->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'filters' => 'array',

            'row_count' => 'integer',

            'generated_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }
}
