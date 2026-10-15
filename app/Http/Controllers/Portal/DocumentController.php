<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function download(
        ClientPortalDocument $document
    ): StreamedResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        abort_unless(
            $document->client_id
            === $portalUser->client_id,
            404
        );

        abort_unless(
            $document->isAvailable(),
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

        activity(
            'client-portal-downloads'
        )
            ->performedOn(
                $document
            )
            ->withProperties([
                'client_portal_user_id' => $portalUser->id,
            ])
            ->event('download')
            ->log(
                'Client portal document downloaded'
            );

        return Storage::disk(
            $document->disk
        )->download(
            $document->path,
            $document->original_name
        );
    }
}
