<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function store(Request $request, DocumentRequest $documentRequest): RedirectResponse
    {
        $user = $request->user();

        abort_unless($documentRequest->assigned_to_user_id === $user->id, 403);

        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        $path = $request->file('file')->store('documents', 'public');
        $file = $request->file('file');

        $document = Document::create([
            'name' => $documentRequest->name,
            'type' => 'other',
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'documentable_type' => 'App\Models\Operation',
            'documentable_id' => $documentRequest->operation_id,
            'uploaded_at' => now(),
        ]);

        $documentRequest->update([
            'status' => 'uploaded',
            'document_id' => $document->id,
        ]);

        return back()->with('success', 'Document déposé avec succès.');
    }
}
