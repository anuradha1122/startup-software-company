<?php

namespace App\Services\Reports;

class ReportCatalogService
{
    public function all(): array
    {
        return [
            [
                'key' => 'project_operations',

                'module' => 'M13',

                'title' => 'Project Operations',

                'description' => 'Cross-module project delivery, team, approval, cost, revenue and receivable report.',

                'excel_url' => '/admin/reports/project-operations/export/excel',

                'pdf_url' => '/admin/reports/project-operations/export/pdf',
            ],

            [
                'key' => 'finance_portfolio',

                'module' => 'M11',

                'title' => 'Finance Portfolio',

                'description' => 'Project profitability portfolio grouped by currency.',

                'excel_url' => '/admin/finance/export/excel',

                'pdf_url' => '/admin/finance/export/pdf',
            ],

            [
                'key' => 'invoices',

                'module' => 'M10',

                'title' => 'Invoice Register',

                'description' => 'Internal invoice register export.',

                'excel_url' => '/admin/invoices/export/excel',

                'pdf_url' => null,
            ],

            [
                'key' => 'payments',

                'module' => 'M10',

                'title' => 'Payments Register',

                'description' => 'Internal client payment register.',

                'excel_url' => '/admin/payments/export/excel',

                'pdf_url' => null,
            ],

            [
                'key' => 'receivables',

                'module' => 'M10',

                'title' => 'Accounts Receivable',

                'description' => 'Outstanding and overdue receivable report.',

                'excel_url' => '/admin/receivables/export/excel',

                'pdf_url' => '/admin/receivables/export/pdf',
            ],

            [
                'key' => 'clients',

                'module' => 'M02',

                'title' => 'Client Register',

                'description' => 'Client directory export.',

                'excel_url' => '/admin/clients/export/excel',

                'pdf_url' => null,
            ],

            [
                'key' => 'leads',

                'module' => 'M01',

                'title' => 'Lead Register',

                'description' => 'CRM lead register and pipeline reporting.',

                'excel_url' => '/admin/leads/export/excel',

                'pdf_url' => '/admin/leads/export/pipeline-pdf',
            ],
        ];
    }
}
