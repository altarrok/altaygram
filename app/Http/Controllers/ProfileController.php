<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;
        $postCount = Cache::remember('count.posts.' . $user->id, now()->addSeconds(30), function () use ($user) {
            return $user->posts()->count();
        });
        $followerCount =  Cache::remember('count.follower.' . $user->id, now()->addSeconds(30), function () use ($user) {
            return $user->profile->followers->count();
        });
        $followingCount = Cache::remember('count.following
        .' . $user->id, now()->addSeconds(30), function () use ($user) {
            return $user->following->count();
        });
        return view('profile/show', compact('user','follows', 'postCount', 'followerCount', 'followingCount'));
    }

    public function edit(User $user) {
        $this->authorize('update', $user->profile);
        return view('profile.edit', compact('user'));
    }

    public function update(User $user) {
        $this->authorize('update', $user->profile);
        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        if (request('image')) {
            $img_path = request('image')->store('profile', 'public');

            $image = Image::make(public_path("storage/{$img_path}"))->fit(1000, 1000);
            $image->save();

            auth()->user()->profile->update(array_merge(
                $data,
                ['image' => $img_path]
            ));
        } else {
            auth()->user()->profile->update($data);
        }

        return redirect("/profile/{$user->id}");
    }
}
