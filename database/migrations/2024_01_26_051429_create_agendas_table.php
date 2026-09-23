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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->date('agenda_date');//This is the day of the agenda like 28th of Dec and in agenda details table add the all list of sessions related to this agenda
            $table->tinyInteger('order')->default(100);
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->on('events')->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists('agendas');
    }
};
