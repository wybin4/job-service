<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('employer_qualities', function (Blueprint $table) {
            $table->id();
            $table->string('quality_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_qualities');
    }
};
