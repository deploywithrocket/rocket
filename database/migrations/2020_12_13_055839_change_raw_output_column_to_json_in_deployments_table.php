<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeRawOutputColumnToJsonInDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Wrap all existing data into a dummy json object
        DB::table('deployments')
            ->select(['id', 'raw_output'])
            ->orderBy('id')
            ->chunk(50, function ($chunk) {
                $chunk->each(function ($deployment) {
                    $raw_output = ['data' => $deployment->raw_output];

                    DB::table('deployments')
                        ->where('id', $deployment->id)
                        ->update(['raw_output' => json_encode($raw_output)]);
                });
            });

        Schema::table('deployments', function (Blueprint $table) {
            $table->json('raw_output')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be rollback'd.
    }
}
