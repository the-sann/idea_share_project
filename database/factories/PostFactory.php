<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        $title = fake()->sentence(6);



        return [
            'title' => $title,
            'body' => fake()->paragraphs(5, true),
            'image' => 'posts/' . fake()->image('public/storage/posts', 640, 480, null, false),
            'slug' => Str::slug($title),
            'author_id' => 1,
            'category_id' => Category::inRandomOrder()->value('id'),
        ];
    }
}
