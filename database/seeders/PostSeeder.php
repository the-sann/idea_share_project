<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $categories = Category::all();
        if ($categories->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            Post::factory()
                ->count(5)
                ->create([
                    'author_id' => $user->id,
                    'category_id' => $categories->random()->id,
                ]);
        }
    }
}
