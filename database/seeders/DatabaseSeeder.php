<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Author::factory(30)->create();
        \App\Models\Category::factory(15)->create();
        \App\Models\Format::factory(3)->create();
        \App\Models\IdentificationType::factory(20)->create();
        \App\Models\Publisher::factory(20)->create();
        \App\Models\Series::factory(10)->create();

        $this->call([
            LibrarySeeder::class,
        ]);
    }
}
