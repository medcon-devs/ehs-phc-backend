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
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('speciality');
            $table->string('title')->nullable()->after('profession');
            $table->string('job_title')->nullable()->after('title');
            $table->string('work_place')->nullable()->after('job_title');
            $table->string('phone')->nullable()->after('work_place');
            $table->boolean('is_ehs')->default(false)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profession',
                'title',
                'job_title',
                'work_place',
                'phone',
                'is_ehs',
            ]);
        });
    }
};
