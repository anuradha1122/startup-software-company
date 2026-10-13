<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectStakeholderRequest;
use App\Models\ClientContact;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ProjectStakeholderController extends Controller
{
    public function store(
        StoreProjectStakeholderRequest $request,
        Project $project
    ): RedirectResponse {
        $data =
            $request->validated();

        if ($data['type'] === 'internal') {
            $user =
                User::findOrFail(
                    $data['user_id']
                );

            $data['name'] =
                $user->name;

            $data['email'] =
                $user->email;

            $data['client_contact_id'] =
                null;
        }

        if ($data['type'] === 'client') {
            $contact =
                ClientContact::findOrFail(
                    $data[
                        'client_contact_id'
                    ]
                );

            abort_unless(
                $contact->client_id
                    === $project->client_id,
                422
            );

            $data['name'] =
                $contact->name;

            $data['email'] =
                $contact->email;

            $data['user_id'] =
                null;
        }

        if (
            $data['is_primary']
            ?? false
        ) {
            $project
                ->stakeholders()
                ->where(
                    'type',
                    $data['type']
                )
                ->update([
                    'is_primary' => false,
                ]);
        }

        $project
            ->stakeholders()
            ->create($data);

        return back()->with(
            'success',
            'Stakeholder added.'
        );
    }
}
