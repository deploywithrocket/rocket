<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('project_id', 26)->nullable();
            $table->char('server_id', 26)->nullable();
            $table->string('status')->default('pending');
            $table->string('release');
            $table->json('commit');

            $table->json('raw_output')->nullable();

            $table->datetime('started_at')->nullable();
            $table->datetime('ended_at')->nullable();

            $table->timestamps();
        });
    }
};
