<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartedAndEndedAtToDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->datetime('started_at')->after('raw_output')->nullable();
            $table->datetime('ended_at')->after('started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('started_at');
            $table->dropColumn('ended_at');
        });
    }
}
