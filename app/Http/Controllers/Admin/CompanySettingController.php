<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCompanySettingRequest;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CompanySettingController extends Controller
{
    public function edit(): Response
    {
        $setting = CompanySetting::query()->firstOrFail();

        Gate::authorize('view', $setting);

        return Inertia::render(
            'admin/company/edit',
            [
                'setting' => $setting,
            ]
        );
    }

    public function update(
        UpdateCompanySettingRequest $request
    ): RedirectResponse {
        $setting = CompanySetting::query()->firstOrFail();

        Gate::authorize('update', $setting);

        $data = $request->safe()->except([
            'logo',
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')
                    ->delete($setting->logo_path);
            }

            $data['logo_path'] = $request
                ->file('logo')
                ->store('branding', 'public');
        }

        $setting->update($data);

        return back()->with(
            'success',
            'Company settings updated.'
        );
    }
}
