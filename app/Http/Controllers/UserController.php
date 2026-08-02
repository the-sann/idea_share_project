<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(StoreProfileRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $profile = $user->profile()->firstOrCreate([]);

        if ($request->hasFile('profile_image')) {

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $path = $request->file('profile_image')
                ->store('profile-images', 'public');

            $profile->profile_image = $path;
        }

        $profile->bio = $validated['bio'] ?? null;
        $profile->career = $validated['career'] ?? null;
        $profile->location = $validated['location'] ?? null;
        $profile->website = $validated['website'] ?? null;
        $profile->birthday = $validated['birthday'] ?? null;

        $profile->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('profile'),
        ]);
    }
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('profile'),
        ]);
    }
}
