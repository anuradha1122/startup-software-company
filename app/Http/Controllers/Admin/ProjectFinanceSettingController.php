<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProjectFinanceSettingRequest;
use App\Models\Project;
use App\Models\ProjectFinanceSetting;
use Illuminate\Http\RedirectResponse;

class ProjectFinanceSettingController extends Controller
{
    public function update(
        UpdateProjectFinanceSettingRequest $request,
        Project $project
    ): RedirectResponse {
        $setting =
            ProjectFinanceSetting::firstOrNew([
                'project_id' => $project->id,
            ]);

        if (! $setting->exists) {
            $setting->created_by =
                $request->user()->id;
        }

        $setting->fill([
            ...$request->validated(),

            'updated_by' => $request->user()->id,
        ]);

        $setting->save();

        return back()->with(
            'success',
            'Project finance settings updated.'
        );
    }
}
