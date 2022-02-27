<?php

namespace Database\Seeders;

use App\Models\FileFormat;
use Illuminate\Database\Seeder;

class FileFormatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FileFormat::factory()->create();
    }
}
