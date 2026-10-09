<?php

namespace App\Policies;

use App\Models\CompanySetting;
use App\Models\User;

class CompanySettingPolicy
{
    public function view(User $user, CompanySetting $setting): bool
    {
        return $user->can('company-settings.view');
    }

    public function update(User $user, CompanySetting $setting): bool
    {
        return $user->can('company-settings.update');
    }
}
