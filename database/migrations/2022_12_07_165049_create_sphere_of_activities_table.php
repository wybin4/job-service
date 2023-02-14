<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up()
    {
        Schema::create('sphere_of_activities', function (Blueprint $table) {
            $table->id();
            $table->string('sphere_of_activity_name');
            $table->timestamps();
        });
        DB::table('sphere_of_activities')->insert([
            'sphere_of_activity_name' => 'Программирование'
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('sphere_of_activities');
    }
};
