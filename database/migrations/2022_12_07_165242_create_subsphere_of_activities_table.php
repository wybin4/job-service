<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subsphere_of_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sphere_id');
            $table->foreign('sphere_id')->references('id')->on('sphere_of_activities')->cascadeOnDelete();
            $table->string('subsphere_of_activity_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('subsphere_of_activities');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
