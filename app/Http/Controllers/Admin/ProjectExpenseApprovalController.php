<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewProjectExpenseRequest;
use App\Models\ProjectExpense;
use App\Models\User;
use App\Notifications\ProjectExpenseApprovedNotification;
use App\Notifications\ProjectExpenseRejectedNotification;
use App\Notifications\ProjectExpenseSubmittedNotification;
use App\Services\Expenses\ProjectExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class ProjectExpenseApprovalController extends Controller
{
    public function submit(
        Request $request,
        ProjectExpense $expense,
        ProjectExpenseService $service
    ): RedirectResponse {
        Gate::authorize(
            'submit',
            $expense
        );

        $service->submit(
            $expense
        );

        $reviewers =
            User::query()
                ->permission(
                    'expenses.review'
                )
                ->where(
                    'status',
                    'active'
                )
                ->where(
                    'id',
                    '!=',
                    $request
                        ->user()
                        ->id
                )
                ->get();

        Notification::send(
            $reviewers,
            new ProjectExpenseSubmittedNotification(
                $expense->load(
                    'project'
                )
            )
        );

        return back()->with(
            'success',
            'Expense submitted.'
        );
    }

    public function review(
        ReviewProjectExpenseRequest $request,
        ProjectExpense $expense,
        ProjectExpenseService $service
    ): RedirectResponse {
        Gate::authorize(
            'review',
            $expense
        );

        $decision =
            $request->validated(
                'decision'
            );

        if ($decision === 'approved') {
            $service->approve(
                $expense,
                $request->user(),
                $request->validated(
                    'review_notes'
                )
            );

            if ($expense->creator) {
                $expense
                    ->creator
                    ->notify(
                        new ProjectExpenseApprovedNotification(
                            $expense
                        )
                    );
            }

            return back()->with(
                'success',
                'Expense approved.'
            );
        }

        $service->reject(
            $expense,
            $request->user(),
            $request->validated(
                'review_notes'
            )
        );

        if ($expense->creator) {
            $expense
                ->creator
                ->notify(
                    new ProjectExpenseRejectedNotification(
                        $expense
                    )
                );
        }

        return back()->with(
            'success',
            'Expense returned for changes.'
        );
    }
}
