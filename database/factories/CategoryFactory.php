<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public static array $categories = [
        'Front-end',
        'Back-end',
        'Devops',
        'JavaScript',
        'React',
        'CSS',
        'Python',
        'PHP',
        'C#',
        'Go',
        'Technology'
    ];

    public static string $currentCategory;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'name' => static::selectRandomCategory(),
        ];
    }

    public static function selectRandomCategory() {
        if (!count(self::$categories)) return 'Rust';

        $randomIndex = rand(0, count(self::$categories) - 1);

        self::$currentCategory = self::$categories[$randomIndex];

        $filtered = array_filter(self::$categories, function($item) {
            return $item !== self::$currentCategory;
        });

        self::$categories = array_values($filtered);

        return self::$currentCategory;
    }
}
