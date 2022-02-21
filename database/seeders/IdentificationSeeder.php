<?php

namespace Database\Seeders;

use App\Models\Identification;
use Illuminate\Database\Seeder;

class IdentificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Identification::factory()->count(5)->create();
    }
}
