<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSkillRequest;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;

class SkillController extends Controller
{
    public function store(
        StoreSkillRequest $request
    ): RedirectResponse {
        Skill::create([
            ...$request->validated(),
            'active' => true,
        ]);

        return back()->with(
            'success',
            'Skill created.'
        );
    }

    public function toggle(
        Skill $skill
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'people.skills.manage'
            ),
            403
        );

        $skill->update([
            'active' => ! $skill->active,
        ]);

        return back()->with(
            'success',
            'Skill status updated.'
        );
    }
}
