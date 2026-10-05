<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        $stock = $this->faker->numberBetween(5, 20);
        return [
            'category_id' => \App\Models\Category::factory(),
            'location_id' => \App\Models\Location::factory(),
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'author' => $this->faker->name(),
            'publisher' => $this->faker->company(),
            'year' => $this->faker->year(),
            'isbn' => $this->faker->unique()->isbn13(),
            'stock' => $stock,
            'available_stock' => $stock,
            'description' => $this->faker->paragraph(),
            'image' => null,
        ];
    }
}
