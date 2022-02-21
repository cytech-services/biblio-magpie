<?php

use App\Models\Book;
use App\Models\IdentificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('identifications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Book::class)->index()->constrained();
            $table->foreignIdFor(IdentificationType::class)->index()->constrained();
            $table->string('value');
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
        Schema::dropIfExists('identifications');
    }
}
