<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->on('events')->references('id')->cascadeOnDelete();
            $table->string('title');
            $table->string('image');
            $table->string('subtitle');
            $table->mediumText('message_header');
            $table->text('message_content');
            $table->tinyInteger('order');
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
        Schema::dropIfExists('event_messages');
    }
};
