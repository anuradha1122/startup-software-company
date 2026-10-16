<?php

namespace App\Services\Operations;

use App\Models\ClientApprovalRequest;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class OperationalHealthService
{
    public function check(): array
    {
        $checks = [];

        $checks[] =
            $this->databaseCheck();

        $checks[] =
            $this->failedJobsCheck();

        $checks[] =
            $this->overdueInvoicesCheck();

        $checks[] =
            $this->overdueApprovalsCheck();

        $checks[] =
            $this->financeSnapshotsCheck();

        $issueCount =
            collect($checks)
                ->whereIn(
                    'status',
                    [
                        'warning',
                        'critical',
                    ]
                )
                ->count();

        $criticalCount =
            collect($checks)
                ->where(
                    'status',
                    'critical'
                )
                ->count();

        return [
            'status' => $criticalCount > 0
                    ? 'critical'
                    : (
                        $issueCount > 0
                            ? 'warning'
                            : 'healthy'
                    ),

            'checked_at' => now()->toIso8601String(),

            'issue_count' => $issueCount,

            'critical_count' => $criticalCount,

            'environment' => app()->environment(),

            'queue_connection' => config(
                'queue.default'
            ),

            'checks' => $checks,
        ];
    }

    private function databaseCheck(): array
    {
        try {
            DB::select('select 1');

            return [
                'key' => 'database',

                'label' => 'Database Connection',

                'status' => 'healthy',

                'value' => 'Connected',

                'message' => 'Database connectivity is available.',
            ];
        } catch (Throwable $exception) {
            report($exception);

            return [
                'key' => 'database',

                'label' => 'Database Connection',

                'status' => 'critical',

                'value' => 'Unavailable',

                'message' => 'The application could not query the database.',
            ];
        }
    }

    private function failedJobsCheck(): array
    {
        if (
            ! Schema::hasTable(
                'failed_jobs'
            )
        ) {
            return [
                'key' => 'failed_jobs',

                'label' => 'Failed Queue Jobs',

                'status' => 'healthy',

                'value' => 0,

                'message' => 'No failed-jobs table is configured.',
            ];
        }

        $count =
            DB::table(
                'failed_jobs'
            )->count();

        return [
            'key' => 'failed_jobs',

            'label' => 'Failed Queue Jobs',

            'status' => $count > 0
                    ? 'warning'
                    : 'healthy',

            'value' => $count,

            'message' => $count > 0
                    ? 'Failed queue jobs require administrative review.'
                    : 'No failed queue jobs were detected.',
        ];
    }

    private function overdueInvoicesCheck(): array
    {
        $count =
            Invoice::query()
                ->where(
                    'status',
                    'overdue'
                )
                ->where(
                    'balance_due',
                    '>',
                    0
                )
                ->count();

        return [
            'key' => 'overdue_invoices',

            'label' => 'Overdue Invoices',

            'status' => $count > 0
                    ? 'warning'
                    : 'healthy',

            'value' => $count,

            'message' => $count > 0
                    ? 'Outstanding overdue invoices require finance follow-up.'
                    : 'No overdue outstanding invoices were detected.',
        ];
    }

    private function overdueApprovalsCheck(): array
    {
        $count =
            ClientApprovalRequest::query()
                ->where(
                    'status',
                    'pending'
                )
                ->whereNotNull(
                    'due_at'
                )
                ->where(
                    'due_at',
                    '<',
                    now()
                )
                ->count();

        return [
            'key' => 'overdue_client_approvals',

            'label' => 'Overdue Client Approvals',

            'status' => $count > 0
                    ? 'warning'
                    : 'healthy',

            'value' => $count,

            'message' => $count > 0
                    ? 'Some client approval requests are beyond their due date.'
                    : 'No overdue client approvals were detected.',
        ];
    }

    private function financeSnapshotsCheck(): array
    {
        if (
            ! Schema::hasTable(
                'project_financial_snapshots'
            )
        ) {
            return [
                'key' => 'finance_snapshots',

                'label' => 'Finance Snapshots',

                'status' => 'warning',

                'value' => null,

                'message' => 'Project financial snapshot storage is unavailable.',
            ];
        }

        $projectCount =
            Project::query()
                ->whereIn(
                    'status',
                    [
                        'active',
                        'on_hold',
                        'completed',
                    ]
                )
                ->count();

        $todaySnapshotProjects =
            DB::table(
                'project_financial_snapshots'
            )
                ->whereDate(
                    'snapshot_date',
                    today()
                )
                ->distinct()
                ->count(
                    'project_id'
                );

        $missing =
            max(
                0,
                $projectCount
                - $todaySnapshotProjects
            );

        return [
            'key' => 'finance_snapshots',

            'label' => 'Daily Finance Snapshots',

            'status' => $missing > 0
                    ? 'warning'
                    : 'healthy',

            'value' => $missing,

            'message' => $missing > 0
                    ? "{$missing} project(s) do not yet have today's financial snapshot."
                    : 'All tracked projects have a financial snapshot for today.',
        ];
    }
}
