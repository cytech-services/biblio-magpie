<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('userables', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->index()->constrained();
            $table->text('userable_type')->index();
            $table->unsignedBigInteger('userable_id')->index();
            $table->timestamps();

            // Restrict the resource to only be shared once per user
            $table->unique(['user_id', 'userable_id', 'userable_type']);
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
        Schema::dropIfExists('userables');
    }
}
