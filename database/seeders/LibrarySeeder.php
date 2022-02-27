<?php

namespace Database\Seeders;

use App\Models\Library;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Author::factory(30)->create();
        \App\Models\Category::factory(15)->create();
        \App\Models\FileFormat::factory(3)->create();
        \App\Models\IdentificationType::factory(20)->create();
        \App\Models\Publisher::factory(20)->create();
        \App\Models\Series::factory(10)->create();

        Library::factory(2)
            ->hasBooks(500)
            ->create();
    }
}
