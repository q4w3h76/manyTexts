<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Text>
 */
class TextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $tags = [];
        for ($i=0; $i < rand(1, 5); $i++) { 
            array_push($tags, fake()->word());
        }
        $tags = json_encode($tags);
        return [
            'text' => fake()->text(),
            'tags' => $tags,
            'is_public' => fake()->boolean(),
            'expiration' => now(),
        ];
    }
}
