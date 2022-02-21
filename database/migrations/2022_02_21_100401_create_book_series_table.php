<?php

use App\Models\Book;
use App\Models\Series;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('book_series', function (Blueprint $table) {
            $table->foreignIdFor(Book::class)->index()->constrained();
            $table->foreignIdFor(Series::class)->index()->constrained();
            $table->unsignedTinyInteger('order');

            $table->unique(['book_id', 'series_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_series');
    }
}
