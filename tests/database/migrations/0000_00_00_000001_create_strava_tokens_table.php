<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('strava_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('athlete_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('strava_tokens');
    }
};
