<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SyncResourceSkillsRequest;
use App\Http\Requests\Admin\UpdateResourceProfileRequest;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PeopleController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'people.view'
            ),
            403
        );

        $people =
            User::query()
                ->with([
                    'roles',
                    'resourceProfile.skills',
                ])
                ->when(
                    $request->filled('search'),
                    function ($query) use (
                        $request
                    ): void {
                        $search =
                            (string) $request->string(
                                'search'
                            );

                        $query->where(
                            function ($inner) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'admin/people/index',
            [
                'people' => $people,

                'filters' => $request->only(
                    'search'
                ),
            ]
        );
    }

    public function show(
        Request $request,
        User $user
    ): Response {
        abort_unless(
            $request->user()->can(
                'people.view'
            ),
            403
        );

        $user->load([
            'roles',
            'resourceProfile.skills',
            'projectAssignments' => fn ($query) => $query
                ->with('project')
                ->latest(),
        ]);

        return Inertia::render(
            'admin/people/show',
            [
                'person' => $user,

                'skills' => Skill::query()
                    ->where(
                        'active',
                        true
                    )
                    ->orderBy('name')
                    ->get(),
            ]
        );
    }

    public function update(
        UpdateResourceProfileRequest $request,
        User $user
    ): RedirectResponse {
        $user
            ->resourceProfile()
            ->updateOrCreate(
                [],
                [
                    ...$request->validated(),

                    'updated_by' => $request->user()->id,
                ]
            );

        return back()->with(
            'success',
            'Resource profile updated.'
        );
    }

    public function syncSkills(
        SyncResourceSkillsRequest $request,
        User $user
    ): RedirectResponse {
        $profile =
            $user
                ->resourceProfile()
                ->firstOrCreate(
                    [],
                    [
                        'available_for_projects' => true,

                        'updated_by' => $request->user()->id,
                    ]
                );

        $sync = [];

        foreach (
            $request->validated(
                'skills'
            ) ?? [] as $skill
        ) {
            $sync[
                $skill['skill_id']
            ] = [
                'proficiency' => $skill[
                        'proficiency'
                    ],

                'years_experience' => $skill[
                        'years_experience'
                    ] ?? null,
            ];
        }

        $profile
            ->skills()
            ->sync($sync);

        return back()->with(
            'success',
            'Skills updated.'
        );
    }
}
