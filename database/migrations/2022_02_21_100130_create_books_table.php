<?php

use App\Models\Author;
use App\Models\Library;
use App\Models\Publisher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Library::class)->constrained();
            $table->string('title');
            $table->string('sub_title')->nullable();
            $table->text('description');
            $table->string('edition')->nullable();
            $table->string('language', 2);
            $table->unsignedInteger('page_count')->nullable();
            $table->foreignIdFor(Publisher::class)->constrained();
            $table->unsignedSmallInteger('rating')->nullable()->default(0);
            $table->boolean('has_media')->default(false);
            $table->date('publish_date');
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
        Schema::dropIfExists('books');
    }
}
