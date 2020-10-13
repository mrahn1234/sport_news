<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_img');
            $table->text('summary');
            $table->text('content')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('hot_or_nor')->default(false); // 1 hot, 0 nor
            $table->integer('status')->default(0);
            $table->dateTime('date_publish');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('slug');
            $table->timestamps();

            $table->foreign('author_id')
                ->references('id')
                ->on('user_infos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
