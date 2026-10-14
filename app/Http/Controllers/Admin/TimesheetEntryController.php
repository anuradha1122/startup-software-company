<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTimesheetEntryRequest;
use App\Http\Requests\Admin\UpdateTimesheetEntryRequest;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Timesheet;
use App\Models\TimesheetEntry;
use App\Services\People\AssignmentEligibilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TimesheetEntryController extends Controller
{
    public function store(
        StoreTimesheetEntryRequest $request,
        Timesheet $timesheet,
        AssignmentEligibilityService $eligibility
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $timesheet
        );

        $entry =
            $this->validatedEntryData(
                $request->validated(),
                $timesheet,
                $eligibility
            );

        $this->assertDailyLimit(
            $timesheet,
            $entry['work_date'],
            (float) $entry['hours']
        );

        $timesheet
            ->entries()
            ->create($entry);

        return back()->with(
            'success',
            'Time entry added.'
        );
    }

    public function update(
        UpdateTimesheetEntryRequest $request,
        Timesheet $timesheet,
        TimesheetEntry $entry,
        AssignmentEligibilityService $eligibility
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $timesheet
        );

        $this->assertBelongs(
            $timesheet,
            $entry
        );

        $data =
            $this->validatedEntryData(
                $request->validated(),
                $timesheet,
                $eligibility
            );

        $this->assertDailyLimit(
            $timesheet,
            $data['work_date'],
            (float) $data['hours'],
            $entry->id
        );

        $entry->update([
            ...$data,

            'hourly_cost_rate_snapshot' => null,

            'internal_cost_amount' => null,

            'cost_currency' => null,

            'costed_at' => null,
        ]);

        return back()->with(
            'success',
            'Time entry updated.'
        );
    }

    public function destroy(
        Timesheet $timesheet,
        TimesheetEntry $entry
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $timesheet
        );

        $this->assertBelongs(
            $timesheet,
            $entry
        );

        $entry->delete();

        return back()->with(
            'success',
            'Time entry removed.'
        );
    }

    private function validatedEntryData(
        array $data,
        Timesheet $timesheet,
        AssignmentEligibilityService $eligibility
    ): array {
        $date =
            Carbon::parse(
                $data['work_date']
            );

        abort_unless(
            $date->betweenIncluded(
                $timesheet->week_start,
                $timesheet->week_end
            ),
            422,
            'The work date must fall within the timesheet week.'
        );

        $project =
            Project::findOrFail(
                $data['project_id']
            );

        $task =
            ProjectTask::findOrFail(
                $data['project_task_id']
            );

        abort_unless(
            $task->project_id
            === $project->id,
            422,
            'The task does not belong to the selected project.'
        );

        $assignment =
            $eligibility
                ->assignmentForDate(
                    $project,
                    $timesheet->user_id,
                    $date
                );

        abort_unless(
            $assignment,
            422,
            'No valid project assignment exists for this work date.'
        );

        return [
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => $date->toDateString(),

            'hours' => $data['hours'],

            'description' => $data['description']
                ?? null,

            'billable' => $data['billable']
                ?? $assignment->billable,
        ];
    }

    private function assertDailyLimit(
        Timesheet $timesheet,
        string $date,
        float $hours,
        ?int $excludeEntryId = null
    ): void {
        $query =
            $timesheet
                ->entries()
                ->whereDate(
                    'work_date',
                    $date
                );

        if ($excludeEntryId) {
            $query->where(
                'id',
                '!=',
                $excludeEntryId
            );
        }

        $total =
            (float) $query
                ->sum('hours')
            + $hours;

        abort_if(
            $total > 24,
            422,
            'Total logged time cannot exceed 24 hours for one day.'
        );
    }

    private function assertBelongs(
        Timesheet $timesheet,
        TimesheetEntry $entry
    ): void {
        abort_unless(
            $entry->timesheet_id
            === $timesheet->id,
            404
        );
    }
}
