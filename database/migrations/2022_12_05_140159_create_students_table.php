<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('university_id')->unsigned()->index();
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('cascade');
            $table->string('email');
            $table->string('student_fio');
            $table->string('password');
            $table->text('location')->nullable();
            $table->boolean('newsletter_subscription')->default('0');
            //
            $table->timestamps();
            ///
            $table->string('image')->nullable();
        });

    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('students');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
