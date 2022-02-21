<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Identification;
use App\Models\IdentificationType;

class IdentificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Identification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'identification_type_id' => IdentificationType::factory(),
            'value' => $this->faker->word,
        ];
    }
}
