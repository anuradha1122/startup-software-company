<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientDocumentRequest;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientDocumentController extends Controller
{
    public function store(
        StoreClientDocumentRequest $request,
        Client $client
    ): RedirectResponse {
        $file = $request->file('file');

        $path = $file->store(
            "clients/{$client->public_id}",
            'local'
        );

        $document = $client
            ->documents()
            ->create([
                'title' => $request->validated(
                    'title'
                ),

                'category' => $request->validated(
                    'category'
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

        activity('client-documents')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $client
            )
            ->event('uploaded')
            ->withProperties([
                'document_id' => $document->public_id,

                'category' => $document->category,

                'original_name' => $document->original_name,
            ])
            ->log(
                'Client document uploaded'
            );

        return back()->with(
            'success',
            'Document uploaded.'
        );
    }

    public function download(
        Client $client,
        ClientDocument $document
    ): StreamedResponse {
        abort_unless(
            request()->user()->can(
                'clients.documents.download'
            ),
            403
        );

        abort_unless(
            $document->client_id
                === $client->id,
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

        activity('client-documents')
            ->causedBy(
                request()->user()
            )
            ->performedOn(
                $client
            )
            ->event('downloaded')
            ->withProperties([
                'document_id' => $document->public_id,

                'original_name' => $document->original_name,
            ])
            ->log(
                'Client document downloaded'
            );

        return Storage::disk(
            $document->disk
        )->download(
            $document->path,
            $document->original_name
        );
    }

    public function destroy(
        Client $client,
        ClientDocument $document
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'clients.documents.manage'
            ),
            403
        );

        abort_unless(
            $document->client_id
                === $client->id,
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
            'Document removed.'
        );
    }
}
