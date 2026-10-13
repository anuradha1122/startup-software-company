<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSubmission;
use App\Models\WorkSubmissionAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkSubmissionAttachmentController extends Controller
{
    public function download(
        WorkSubmission $submission,
        WorkSubmissionAttachment $attachment
    ): StreamedResponse {
        abort_unless(
            request()->user()->can(
                'work-submissions.attachments.download'
            ),
            403
        );

        abort_unless(
            $attachment->work_submission_id
            === $submission->id,
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
            $attachment->original_name
        );
    }
}
