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
        Schema::create('event_faculty', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->on('events')->references('id')->cascadeOnDelete();
            $table->unsignedBigInteger('faculty_id');
            $table->foreign('faculty_id')->on('faculties')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('event_faculty');
    }
};
