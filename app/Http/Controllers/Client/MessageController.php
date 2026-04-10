<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Operation $operation): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $operation->assignedUsers()->where('user_id', $user->id)->exists(),
            403
        );

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $operation->messages()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Message envoyé.');
    }
}
