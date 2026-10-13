<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectDecisionRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class ProjectDecisionController extends Controller
{
    public function store(
        StoreProjectDecisionRequest $request,
        Project $project
    ): RedirectResponse {
        $project
            ->decisions()
            ->create([
                ...$request->validated(),

                'decided_by' => $request->user()->id,
            ]);

        return back()->with(
            'success',
            'Governance decision recorded.'
        );
    }
}
