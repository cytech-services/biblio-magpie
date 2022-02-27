<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\BookSeries;
use App\Models\Series;

class BookSeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookSeries::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'series_id' => Series::factory(),
            'book_id' => Book::factory(),
            'order' => $this->faker->numberBetween(-1000, 1000),
        ];
    }
}
