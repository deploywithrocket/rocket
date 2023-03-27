<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('user_id', 26);

            $table->string('name');

            $table->string('ssh_host');
            $table->unsignedInteger('ssh_port')->default(22);
            $table->string('ssh_user');

            $table->string('cmd_git')->nullable();
            $table->string('cmd_npm')->nullable();
            $table->string('cmd_yarn')->nullable();
            $table->string('cmd_php')->nullable();
            $table->string('cmd_composer')->nullable();
            $table->string('cmd_composer_options')->nullable();

            $table->string('status');

            $table->timestamps();
        });
    }
};
