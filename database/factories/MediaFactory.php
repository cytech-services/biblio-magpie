<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Format;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'format_id' => function (array $attributes) {
                return Format::inRandomOrder()->first()->id;
            },
            'path' => $this->faker->file('/tmp', '/' . storage_path('app/public'), false),
            'size' => function (array $attributes) {
                return Storage::disk('public')->size('/' . $attributes['path']);
            }
        ];
    }
}
