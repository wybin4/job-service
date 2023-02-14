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
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });
        /*DB::table('universities')->insert([
            'name' => 'РГЭУ(РИНХ)',
            'email' => 'main@rsue.ru',
            'password' => Hash::make('sds2013sds')
        ]);*/
    }

    public function down()
    {
        Schema::dropIfExists('universities');
    }
};
