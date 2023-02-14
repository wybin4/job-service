<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subsphere_id');
            $table->foreign('subsphere_id')->references('id')->on('subsphere_of_activities')->cascadeOnDelete();
            $table->string('profession_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('professions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
};
