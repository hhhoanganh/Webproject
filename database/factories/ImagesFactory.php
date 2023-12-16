<?php

namespace Database\Factories;

use App\Models\Product\Images;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ImagesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Images::class;

    public function definition()
    {
        return [
            'name' => 'image' . $this->faker->numberBetween(1, 5) . '.jpg',
        ];
    }
}
