<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deployment_tasks', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('deployment_id', 26)->nullable();
            $table->char('server_id', 26)->nullable();
            $table->string('name');
            $table->string('status')->default('pending');
            $table->json('commands')->nullable();
            $table->longText('output')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('ended_at')->nullable();

            $table->timestamps();
        });
    }
};
