<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first, then the column
            $table->dropForeign(['code_id']);   // assumes name users_code_id_foreign
            $table->dropColumn('code_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('code_id')->nullable();
            $table->foreign('code_id')
                  ->references('id')
                  ->on('user_codes')
                  ->cascadeOnDelete();
        });
    }
};
