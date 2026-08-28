<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'profile' => new ProfileResource(
                $this->whenLoaded('profile')
            ),
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
            'is_following' => Auth::check()
                ? $this->followers()
                ->where('follower_id', Auth::id())
                ->exists()
                : false,
        ];
    }
}
