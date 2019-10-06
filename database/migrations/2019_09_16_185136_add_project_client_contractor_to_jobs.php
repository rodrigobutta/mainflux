<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectClientContractorToJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('contractor_id')->unsigned()->nullable();
            $table->integer('project_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['client_id']);
            $table->dropColumn(['contractor_id']);
            $table->dropColumn(['project_id']);
        });
    }
}
