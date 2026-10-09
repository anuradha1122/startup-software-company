<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CompanySetting extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'bank_account_number' => 'encrypted',
            'bank_swift_code' => 'encrypted',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('company-settings')
            ->logOnly([
                'legal_name',
                'trading_name',
                'registration_number',
                'tax_number',
                'email',
                'phone',
                'website',
                'address',
                'country',
                'currency',
                'timezone',
                'logo_path',
                'bank_name',
                'bank_branch',
                'bank_account_name',
                'document_footer',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
