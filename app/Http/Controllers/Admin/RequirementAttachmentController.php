<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequirementAttachmentRequest;
use App\Models\Requirement;
use App\Models\RequirementAttachment;
use App\Models\RequirementVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequirementAttachmentController extends Controller
{
    public function store(
        StoreRequirementAttachmentRequest $request,
        Requirement $requirement,
        RequirementVersion $version
    ): RedirectResponse {
        $this->assertVersion(
            $requirement,
            $version
        );

        $file =
            $request->file('file');

        $path =
            $file->store(
                "requirements/{$requirement->public_id}/v{$version->version_number}",
                'local'
            );

        $version
            ->attachments()
            ->create([
                'title' => $request->validated(
                    'title'
                ),

                'visibility' => $request->validated(
                    'visibility'
                ),

                'original_name' => $file
                    ->getClientOriginalName(),

                'disk' => 'local',

                'path' => $path,

                'mime_type' => $file->getMimeType(),

                'size' => $file->getSize(),

                'uploaded_by' => $request->user()->id,
            ]);

        return back()->with(
            'success',
            'Requirement attachment uploaded.'
        );
    }

    public function download(
        Requirement $requirement,
        RequirementVersion $version,
        RequirementAttachment $attachment
    ): StreamedResponse {
        abort_unless(
            request()->user()->can(
                'requirements.attachments.download'
            ),
            403
        );

        $this->assertVersion(
            $requirement,
            $version
        );

        abort_unless(
            $attachment->requirement_version_id
                === $version->id,
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

    public function destroy(
        Requirement $requirement,
        RequirementVersion $version,
        RequirementAttachment $attachment
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'requirements.attachments.manage'
            ),
            403
        );

        $this->assertVersion(
            $requirement,
            $version
        );

        abort_unless(
            $attachment->requirement_version_id
                === $version->id,
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
            'Attachment deleted.'
        );
    }

    private function assertVersion(
        Requirement $requirement,
        RequirementVersion $version
    ): void {
        abort_unless(
            $version->requirement_id
                === $requirement->id,
            404
        );
    }
}
