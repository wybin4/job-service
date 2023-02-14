<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up()
    {
        Schema::create('resume_skill_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_skill_id');
            $table->foreign('student_skill_id')->references('id')->on('student_skills')->cascadeOnDelete();
            $table->unsignedBigInteger('employer_id');
            $table->foreign('employer_id')->references('id')->on('employers')->cascadeOnDelete();
            $table->integer('skill_rate')->default(1);
            $table->timestamps();
        });
    }


    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('resume_skill_rates');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
};
