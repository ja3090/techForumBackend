<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = DB::select('select id from categories');
        $randomNumber = rand(0, count($categories) - 1);
        $paragraph = $this->faker->paragraphs(2, true);

        return [
            'subject' => $this->faker->sentence(),
            'content' => $paragraph,
            'user_id' => User::factory(),
            'category_id' => $categories[$randomNumber]->id,
            'posted_date' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}
