<?php

namespace App\Services\Leave;

use App\Models\EmployeeProfile;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveRequestReviewedNotification;
use App\Notifications\LeaveRequestSubmittedNotification;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class LeaveRequestService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly LeaveDayCalculator $days,
        private readonly LeaveBalanceService $balances
    ) {}

    public function submit(
        EmployeeProfile $employee,
        array $data
    ): LeaveRequest {
        $leaveType =
            LeaveType::query()
                ->whereKey(
                    $data[
                        'leave_type_id'
                    ]
                )
                ->where(
                    'active',
                    true
                )
                ->firstOrFail();

        $days =
            $this->days->calculate(
                $data['start_date'],
                $data['end_date']
            );

        $this->balances->ensure(
            $employee,
            $leaveType,
            (int) substr(
                $data['start_date'],
                0,
                4
            )
        );

        $overlap =
            LeaveRequest::query()
                ->where(
                    'employee_profile_id',
                    $employee->id
                )
                ->whereIn(
                    'status',
                    [
                        'submitted',
                        'approved',
                    ]
                )
                ->whereDate(
                    'start_date',
                    '<=',
                    $data['end_date']
                )
                ->whereDate(
                    'end_date',
                    '>=',
                    $data['start_date']
                )
                ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'This leave request overlaps another active leave request.',
            ]);
        }

        $request =
            LeaveRequest::create([
                'leave_number' => $this->numbers->next(
                    'leave'
                ),

                'employee_profile_id' => $employee->id,

                'leave_type_id' => $leaveType->id,

                'start_date' => $data['start_date'],

                'end_date' => $data['end_date'],

                'days' => $days,

                'reason' => $data['reason']
                    ?? null,

                'status' => 'submitted',

                'submitted_at' => now(),
            ]);

        $recipients =
            User::query()
                ->permission(
                    'leave-requests.review'
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        Notification::send(
            $recipients,
            new LeaveRequestSubmittedNotification(
                $request
            )
        );

        return $request;
    }

    public function review(
        LeaveRequest $request,
        User $reviewer,
        string $decision,
        ?string $notes
    ): LeaveRequest {
        return DB::transaction(
            function () use (
                $request,
                $reviewer,
                $decision,
                $notes
            ): LeaveRequest {
                $locked =
                    LeaveRequest::query()
                        ->with([
                            'leaveType',
                            'employee.user',
                        ])
                        ->lockForUpdate()
                        ->findOrFail(
                            $request->id
                        );

                if (
                    $locked->status
                    !== 'submitted'
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Only submitted leave requests may be reviewed.',
                    ]);
                }

                if (
                    $decision === 'rejected'
                    && blank($notes)
                ) {
                    throw ValidationException::withMessages([
                        'review_notes' => 'Review notes are required when rejecting leave.',
                    ]);
                }

                if (
                    $locked
                        ->employee
                        ->user_id
                    === $reviewer->id
                ) {
                    throw ValidationException::withMessages([
                        'reviewer' => 'Employees cannot approve or reject their own leave request.',
                    ]);
                }

                if (
                    $decision === 'approved'
                ) {
                    $this
                        ->balances
                        ->consumeApproved(
                            $locked
                        );
                }

                $locked->update([
                    'status' => $decision,

                    'reviewed_by' => $reviewer->id,

                    'review_notes' => $notes,

                    'reviewed_at' => now(),

                    'approved_at' => $decision === 'approved'
                            ? now()
                            : null,

                    'rejected_at' => $decision === 'rejected'
                            ? now()
                            : null,
                ]);

                $locked
                    ->employee
                    ->user
                    ->notify(
                        new LeaveRequestReviewedNotification(
                            $locked->refresh()
                        )
                    );

                return $locked->refresh();
            }
        );
    }
}
