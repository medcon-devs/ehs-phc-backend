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
        Schema::create('agenda_details', function (Blueprint $table) {
            $table->id();
            $table->string('agenda_time');
            $table->enum('type', ['header', 'session'])->default('session');
            $table->string('title');
            $table->string('subtitle');
            $table->boolean('colored')->default(false);
            $table->tinyInteger('order')->default(100);
            $table->unsignedBigInteger('agenda_id');
            $table->foreign('agenda_id')->on('agendas')->references('id')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agenda_details');
    }
};
