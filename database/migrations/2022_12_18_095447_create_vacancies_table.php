<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('vacancies')) {
            return;
        }
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->foreign('employer_id')->references('id')->on('employers')->cascadeOnDelete();
            //
            //
            $table->bigInteger('profession_id')->unsigned()->index();
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
            //
            $table->bigInteger('type_of_employment_id')->unsigned()->index();
            $table->foreign('type_of_employment_id')->references('id')->on('type_of_employments')->onDelete('cascade');
            //
            $table->bigInteger('work_type_id')->unsigned()->index();
            $table->foreign('work_type_id')->references('id')->on('work_types')->onDelete('cascade');
            //
            //
            $table->bigInteger('salary')->default('0');
            $table->integer('work_experience')->default('0');
            $table->string('location')->nullable();
            $table->string('contacts');
            //
            $table->mediumText('description')->nullable();
            $table->boolean('status')->default('0');
            $table->timestamp('archived_at');

            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('vacancies');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
