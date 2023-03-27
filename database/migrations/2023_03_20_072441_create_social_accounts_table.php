<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->char('user_id', 26)->nullable();

            $table->string('provider');
            $table->string('provider_user_id');
            $table->string('provider_user_name');
            $table->string('token');

            $table->timestamps();
        });
    }
};
