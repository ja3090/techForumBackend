<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
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
        $threads = DB::select('select id from threads');
        $users = DB::select('select id from users');
        $randomThreadIndex = rand(0, count($threads) - 1);
        $randomUserIndex = rand(0, count($users) - 1);
        $paragraph = $this->faker->paragraphs(1, true);

        return [
            'content' => $paragraph,
            'user_id' => $users[$randomUserIndex]->id,
            'posted_date' => $this->faker->dateTimeBetween('-1 year'),
            'thread_id' => $threads[$randomThreadIndex]->id,
        ];
    }
}
