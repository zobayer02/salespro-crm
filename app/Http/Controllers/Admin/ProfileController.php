<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => ['nullable', 'image', 'max:3072'],
        ]);

        $data = [
            'name' => $validated['name'],
            'designation' => $validated['designation'],
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $this->storeProfilePhotoAsWebp($request);

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
        }

        $user->update($data);

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    private function storeProfilePhotoAsWebp(Request $request): string
    {
        $image = imagecreatefromstring(file_get_contents($request->file('profile_photo')->getRealPath()));

        if (! $image) {
            throw ValidationException::withMessages([
                'profile_photo' => 'The profile photo could not be processed.',
            ]);
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        Storage::disk('public')->makeDirectory('profile-photos');

        $path = 'profile-photos/' . Str::uuid() . '.webp';
        $saved = imagewebp($image, Storage::disk('public')->path($path), 85);

        imagedestroy($image);

        if (! $saved) {
            throw ValidationException::withMessages([
                'profile_photo' => 'The profile photo could not be saved.',
            ]);
        }

        return $path;
    }
}
