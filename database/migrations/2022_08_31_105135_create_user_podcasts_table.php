<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPodcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_podcasts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('podcast_id');
            $table->string('status', 100);
            $table->timestamp('latest_play')->nullable();
            $table->string('played_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_podcasts');
    }
}
