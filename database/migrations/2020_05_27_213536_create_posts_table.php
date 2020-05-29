<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_external')->unsigned();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
            $table->index('user_id');
            $table->index('id_external');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Create fulltext index
        // WARNING: This is MySql specific
        DB::statement('ALTER TABLE posts ADD FULLTEXT posts_fulltext_index (title)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
