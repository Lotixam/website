<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        return view('contact', [
            'prev' => $request->query('prev'),
            'maxAttachments' => config('lotixam.contact_max_attachments'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $maxFiles = (int) config('lotixam.contact_max_attachments');
        $maxKb = (int) config('lotixam.contact_max_attachment_kb');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mail' => ['required', 'email'],
            'tel' => ['nullable', 'string', 'max:50'],
            'msg' => ['required', 'string'],
            'attachments' => ['nullable', 'array', 'max:'.$maxFiles],
            'attachments.*' => ['file', 'max:'.$maxKb],
        ]);

        $submission = ContactSubmission::create([
            'name' => strip_tags($validated['name']),
            'email' => $validated['mail'],
            'phone' => strip_tags($validated['tel'] ?? ''),
            'message' => $validated['msg'],
            'source_page' => $request->query('prev') ? (string) $request->query('prev') : null,
        ]);

        $files = array_filter($request->file('attachments') ?? []);

        foreach ($files as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $path = $file->store('contact-submissions/'.$submission->id, 'local');

            $submission->attachments()->create([
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return back()->with('message_sent', true);
    }
}
