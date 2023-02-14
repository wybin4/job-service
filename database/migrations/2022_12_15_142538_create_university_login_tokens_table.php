<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('university_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id');
            $table->foreign('university_id')->references('id')->on('universities')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('university_login_tokens');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
