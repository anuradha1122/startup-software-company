<?php

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Str;

function createM16Supplier(
    ?User $creator = null,
    array $overrides = []
): Supplier {
    $creator ??=
        User::factory()->create();

    return Supplier::create(
        array_merge(
            [
                'public_id' => (string) Str::ulid(),

                'supplier_number' => 'SUP-'.Str::upper(
                    Str::random(8)
                ),

                'name' => 'M16 Test Supplier',

                'email' => 'supplier-'
                    .Str::lower(
                        Str::random(8)
                    )
                    .'@example.com',

                'phone' => '0712345678',

                'address' => 'Test Address',

                'status' => 'active',

                'created_by' => $creator->id,
            ],
            $overrides
        )
    );
}
