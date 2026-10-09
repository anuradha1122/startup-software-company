<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DocumentNumberService
{
    public function next(string $type): string
    {
        return DB::transaction(function () use ($type): string {
            $sequence = DocumentSequence::query()
                ->where('type', $type)
                ->where('active', true)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                throw new RuntimeException(
                    "No active document sequence is configured for [{$type}]."
                );
            }

            $number = $sequence->prefix
                .str_pad(
                    (string) $sequence->next_number,
                    $sequence->padding,
                    '0',
                    STR_PAD_LEFT
                );

            $sequence->increment('next_number');

            return $number;
        }, 3);
    }
}
