<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTaskCommentRequest;
use App\Models\ProjectTask;
use Illuminate\Http\RedirectResponse;

class ProjectTaskCommentController extends Controller
{
    public function store(
        StoreProjectTaskCommentRequest $request,
        ProjectTask $task
    ): RedirectResponse {
        $task
            ->comments()
            ->create([
                ...$request->validated(),

                'created_by' => $request->user()->id,
            ]);

        return back()->with(
            'success',
            'Comment added.'
        );
    }
}
