<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectExpense;
use App\Models\ProjectExpenseAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectExpenseAttachmentController extends Controller
{
    public function store(
        Request $request,
        ProjectExpense $expense
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'expenses.attachments.manage'
            ),
            403
        );

        abort_unless(
            $expense->isEditable(),
            409
        );

        $validated =
            $request->validate([
                'type' => [
                    'required',
                    'in:receipt,supplier_invoice,quotation,payment_evidence,other',
                ],

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'file' => [
                    'required',
                    'file',
                    'max:10240',
                    'mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,txt',
                ],
            ]);

        $file =
            $request->file('file');

        $path =
            $file->store(
                "project-expenses/{$expense->public_id}",
                'local'
            );

        $expense
            ->attachments()
            ->create([
                'type' => $validated['type'],

                'title' => $validated['title'],

                'original_name' => $file
                    ->getClientOriginalName(),

                'disk' => 'local',

                'path' => $path,

                'mime_type' => $file
                    ->getMimeType(),

                'size' => $file
                    ->getSize(),

                'uploaded_by' => $request
                    ->user()
                    ->id,
            ]);

        return back()->with(
            'success',
            'Attachment uploaded.'
        );
    }

    public function download(
        ProjectExpense $expense,
        ProjectExpenseAttachment $attachment
    ): StreamedResponse {
        abort_unless(
            request()->user()->can(
                'expenses.attachments.download'
            ),
            403
        );

        abort_unless(
            $attachment
                ->project_expense_id
            === $expense->id,
            404
        );

        abort_unless(
            Storage::disk(
                $attachment->disk
            )->exists(
                $attachment->path
            ),
            404
        );

        return Storage::disk(
            $attachment->disk
        )->download(
            $attachment->path,
            $attachment
                ->original_name
        );
    }

    public function destroy(
        ProjectExpense $expense,
        ProjectExpenseAttachment $attachment
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'expenses.attachments.manage'
            ),
            403
        );

        abort_unless(
            $expense->isEditable(),
            409
        );

        abort_unless(
            $attachment
                ->project_expense_id
            === $expense->id,
            404
        );

        Storage::disk(
            $attachment->disk
        )->delete(
            $attachment->path
        );

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment removed.'
        );
    }
}
