<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContractDocumentRequest;
use App\Models\Contract;
use App\Models\ContractDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractDocumentController extends Controller
{
    public function store(
        StoreContractDocumentRequest $request,
        Contract $contract
    ): RedirectResponse {
        $file =
            $request->file('file');

        $path =
            $file->store(
                "contracts/{$contract->public_id}",
                'local'
            );

        $contract
            ->documents()
            ->create([
                'type' => $request->validated(
                    'type'
                ),

                'title' => $request->validated(
                    'title'
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
            'Contract document uploaded.'
        );
    }

    public function download(
        Contract $contract,
        ContractDocument $document
    ): StreamedResponse {
        abort_unless(
            request()->user()->can(
                'contracts.documents.download'
            ),
            403
        );

        abort_unless(
            $document->contract_id
                === $contract->id,
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
}
