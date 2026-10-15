<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientPortalDocumentRequest;
use App\Models\ClientPortalDocument;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientPortalDocumentController extends Controller
{
    public function index(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'client-portal-documents.view'
            ),
            403
        );

        return Inertia::render(
            'admin/projects/portal-documents/index',
            [
                'project' => [
                    'public_id' => $project->public_id,

                    'project_number' => $project->project_number,

                    'name' => $project->name,
                ],

                'documents' => $project
                    ->portalDocuments()
                    ->latest(
                        'published_at'
                    )
                    ->get(),
            ]
        );
    }

    public function store(
        StoreClientPortalDocumentRequest $request,
        Project $project
    ): RedirectResponse {
        $data =
            $request->validated();

        $file =
            $request->file(
                'file'
            );

        $path =
            $file->store(
                "client-portal/{$project->public_id}",
                'local'
            );

        $project
            ->portalDocuments()
            ->create([
                'client_id' => $project->client_id,

                'category' => $data['category'],

                'title' => $data['title'],

                'description' => $data['description']
                    ?? null,

                'original_name' => $file
                    ->getClientOriginalName(),

                'disk' => 'local',

                'path' => $path,

                'mime_type' => $file
                    ->getMimeType(),

                'size' => $file
                    ->getSize(),

                'published_at' => now(),

                'expires_at' => $data['expires_at']
                    ?? null,

                'uploaded_by' => $request
                    ->user()
                    ->id,
            ]);

        return back()->with(
            'success',
            'Document published to the client portal.'
        );
    }

    public function download(
        Request $request,
        Project $project,
        ClientPortalDocument $document
    ): StreamedResponse {
        abort_unless(
            $request->user()->can(
                'client-portal-documents.download'
            ),
            403
        );

        abort_unless(
            $document->project_id
            === $project->id,
            404
        );

        abort_unless(
            Storage::disk(
                $document->disk
            )->exists(
                $document->path
            ),
            404
        );

        return Storage::disk(
            $document->disk
        )->download(
            $document->path,
            $document->original_name
        );
    }

    public function destroy(
        Request $request,
        Project $project,
        ClientPortalDocument $document
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'client-portal-documents.manage'
            ),
            403
        );

        abort_unless(
            $document->project_id
            === $project->id,
            404
        );

        Storage::disk(
            $document->disk
        )->delete(
            $document->path
        );

        $document->delete();

        return back()->with(
            'success',
            'Portal document removed.'
        );
    }
}
