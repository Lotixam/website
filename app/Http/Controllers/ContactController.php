<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        return view('contact', [
            'prev' => $request->query('prev'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mail' => ['required', 'email'],
            'tel' => ['nullable', 'string', 'max:50'],
            'msg' => ['required', 'string'],
            'join1' => ['nullable', 'file', 'max:10240'],
            'join2' => ['nullable', 'file', 'max:10240'],
            'join3' => ['nullable', 'file', 'max:10240'],
        ]);

        $name = strip_tags($validated['name']);
        $email = $validated['mail'];
        $tel = strip_tags($validated['tel'] ?? '');
        $messageBody = $validated['msg'];

        $text = "Nom: {$name}\n";
        $text .= "Email: {$email}\n\n";
        $text .= "Tel: {$tel}\n\n";
        $text .= "Message:\n{$messageBody}\n";

        $adminTo = config('lotixam.mail_to');
        $fromAddress = config('lotixam.mail_from_address');
        $fromName = config('lotixam.mail_from_name');

        $attachments = array_filter([
            $request->file('join1'),
            $request->file('join2'),
            $request->file('join3'),
        ]);

        $bodyHtml = nl2br(e($text), false);

        foreach ([$adminTo, $email] as $recipient) {
            Mail::html($bodyHtml, function ($message) use ($recipient, $name, $email, $fromAddress, $fromName, $attachments): void {
                $message->to($recipient)
                    ->subject('Contact de '.$name)
                    ->from($fromAddress, $fromName)
                    ->replyTo($email, $name);

                foreach ($attachments as $file) {
                    if ($file && $file->isValid()) {
                        $message->attach($file->getRealPath(), ['as' => $file->getClientOriginalName()]);
                    }
                }
            });
        }

        return back()->with('message_sent', true);
    }
}
