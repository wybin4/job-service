<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unsignedBigInteger('vacancy_id');
            $table->foreign('vacancy_id')->references('id')->on('vacancies')->cascadeOnDelete();
            $table->integer('status')->default('0');
            $table->boolean('type');
            $table->json('data')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('interactions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
