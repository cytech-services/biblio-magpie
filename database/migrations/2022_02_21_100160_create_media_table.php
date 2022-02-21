<?php

use App\Models\Book;
use App\Models\Format;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Book::class)->index()->constrained();
            $table->foreignIdFor(Format::class)->index()->constrained();
            $table->text('path');
            $table->unsignedInteger('size');
            $table->softDeletes();
            $table->timestamps();
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
        Schema::dropIfExists('media');
    }
}
