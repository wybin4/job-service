<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('location')->nullable();
            $table->mediumText('description')->nullable();
            $table->timestamps();
            $table->string('image')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employers');
    }
};
