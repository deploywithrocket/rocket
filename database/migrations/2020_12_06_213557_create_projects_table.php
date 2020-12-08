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
            $table->char('user_id', 26);
            $table->char('server_id', 26);

            $table->string('name');
            $table->string('repository');
            $table->string('branch');
            $table->string('environment');
            $table->string('deploy_path');

            $table->string('live_url')->nullable();

            $table->longText('env')->nullable();
            $table->longText('cron_jobs')->nullable();

            $table->integer('keep_releases')->default(5);

            $table->json('linked_dirs')->nullable();
            $table->json('linked_files')->nullable();
            $table->json('copied_dirs')->nullable();
            $table->json('copied_files')->nullable();

            $table->json('presets')->nullable();
            $table->json('hooks')->nullable();

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
