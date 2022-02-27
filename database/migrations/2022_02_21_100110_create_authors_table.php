<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('goodreads_id')->nullable();
            $table->longText('about')->nullable();
            $table->string('hometown')->nullable();
            $table->date('born_at')->nullable();
            $table->date('died_at')->nullable();
            $table->integer('num_works')->nullable();
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
        Schema::dropIfExists('authors');
    }
}
