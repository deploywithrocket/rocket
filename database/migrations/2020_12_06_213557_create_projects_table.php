<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('server_id', 26)->nullable();

            $table->string('name');
            $table->string('repository_url');
            $table->string('deploy_path');
            $table->string('health_url')->nullable();

            $table->longText('env');
            $table->longText('cron_jobs')->nullable();

            $table->integer('keep_releases')->default(5);

            $table->json('linked_dirs')->nullable();
            $table->json('linked_files')->nullable();
            $table->json('copied_dirs')->nullable();
            $table->json('copied_files')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
