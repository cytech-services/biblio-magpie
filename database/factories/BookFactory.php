<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Identification;
use App\Models\Library;
use App\Models\Media;
use App\Models\Publisher;
use App\Models\Series;
use Illuminate\Support\Facades\Log;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'library_id' => function (array $attributes) {
                return Library::inRandomOrder()->first()->id;
            },
            'title' => $this->faker->sentence(4),
            'sub_title' => $this->faker->word,
            'description' => $this->faker->text,
            'edition' => $this->faker->word,
            'language' => $this->faker->languageCode(),
            'page_count' => $this->faker->numberBetween(100, 1000),
            'publisher_id' => function (array $attributes) {
                return Publisher::inRandomOrder()->first()->id;
            },
            'rating' => $this->faker->numberBetween(1, 5),
            'publish_date' => $this->faker->dateTime(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Book $book) {
            // Attach random authors to the book
            $authors = Author::inRandomOrder()->limit(rand(1, 3))->get()->pluck('id');
            $book->authors()->sync($authors);

            // Randomly decide if a book should be added to a series
            if (rand(1, 5) > 4) {
                $series = Series::inRandomOrder()->first();
                $book->series()->attach($series->id, ['order' => rand(1, 100)]);
            }

            // Attach random categories to the book
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->get()->pluck('id');
            Log::info('Categories: ' . print_r($categories, true));
            $book->categories()->sync($categories);

            // Create and attach random book identifiers
            $identifications = Identification::factory(rand(1, 2))
                ->for($book)
                ->create();

            // Create random book media
            $media = Media::factory(rand(1, 3))
                ->for($book)
                ->create();
        });
    }
}
