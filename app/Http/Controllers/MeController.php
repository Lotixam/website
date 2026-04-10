<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class MeController extends Controller
{
    public function show(Request $request): View
    {
        return view('me.index', ['user' => $request->user()]);
    }

    public function edit(Request $request): View
    {
        return view('me.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$request->user()->id,
            'email' => 'required|email|unique:users,email,'.$request->user()->id,
            'avatar' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,gif,webp'],
        ]);

        $file = $request->file('avatar');
        if ($file) {
            $validated['avatar'] = $file->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        $request->user()->update($validated);

        return back()->with('success', 'Informations mises à jour.');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $request->user()->update(['avatar' => null]);

        return back()->with('success', 'Photo de profil supprimée.');
    }

    public function editPassword(): View
    {
        return view('me.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
