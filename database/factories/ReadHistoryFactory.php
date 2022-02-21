<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\ReadHistory;
use App\Models\User;

class ReadHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReadHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'status' => $this->faker->randomElement(["started", "progress", "finished"]),
            'progress' => $this->faker->text,
        ];
    }
}
