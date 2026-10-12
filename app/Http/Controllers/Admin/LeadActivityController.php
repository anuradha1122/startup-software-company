<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeadActivityRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LeadActivityController extends Controller
{
    public function store(
        StoreLeadActivityRequest $request,
        Lead $lead
    ): RedirectResponse {
        Gate::authorize('update', $lead);

        $validated = $request->validated();

        DB::transaction(
            function () use (
                $lead,
                $validated,
                $request
            ): void {
                $lead->activities()->create([
                    'type' => $validated['type'],

                    'subject' => $validated['subject'],

                    'description' => $validated['description']
                        ?? null,

                    'due_at' => $validated['due_at']
                        ?? null,

                    'completed_at' => ($validated['completed']
                            ?? false)
                            ? now()
                            : null,

                    'created_by' => $request->user()->id,
                ]);

                $updates = [
                    'last_activity_at' => now(),
                    'stale_notified_at' => null,
                ];

                if (
                    $validated['type']
                        === 'follow_up'
                    && ! empty(
                        $validated['due_at']
                    )
                ) {
                    $updates[
                        'next_follow_up_at'
                    ] = $validated['due_at'];

                    $updates[
                        'follow_up_reminded_at'
                    ] = null;
                }

                $lead->updateQuietly(
                    $updates
                );
            }
        );

        return back()->with(
            'success',
            'Activity added.'
        );
    }
}
