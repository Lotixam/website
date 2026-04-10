<?php

namespace App\Http\Controllers\Client;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->loadMissing('profile');
        if (! $user->profile) {
            $user->profile()->create([]);
            $user->load('profile');
        }

        return view('client.profile.edit', ['user' => $user]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->loadMissing('profile');
        if (! $user->profile) {
            $user->profile()->create([]);
            $user->load('profile');
        }

        $request->merge([
            'gender' => filled($request->string('gender')->toString()) ? $request->gender : null,
        ]);

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => ['nullable', new Enum(Gender::class)],
            'phone' => 'nullable|string|max:32',
        ]);

        $displayName = trim(implode(' ', array_filter([$validated['first_name'] ?? '', $validated['last_name'] ?? ''])));
        $user->update([
            'email' => $validated['email'],
            'name' => $displayName !== '' ? $displayName : $user->name,
        ]);

        $user->profile->update([
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Profil mis à jour.');
    }
}
