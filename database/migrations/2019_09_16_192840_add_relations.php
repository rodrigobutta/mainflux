<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('projects', function(Blueprint $table)
        {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');            
            $table->foreign('top_project_id')->references('id')->on('projects')->onDelete('cascade');            
        });

        Schema::table('assets', function(Blueprint $table)
        {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');            
        });
        
        Schema::table('jobs', function(Blueprint $table)
        {
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('jobs', function(Blueprint $table)
        {
            $table->dropForeign('jobs_client_id_foreign');
            $table->dropForeign('jobs_contractor_id_foreign');
            $table->dropForeign('jobs_project_id_foreign');
        });

        Schema::table('assets', function(Blueprint $table)
        {
            $table->dropForeign('assets_client_id_foreign');
            $table->dropForeign('assets_department_id_foreign');
        });

        Schema::table('projects', function(Blueprint $table)
        {
            $table->dropForeign('projects_client_id_foreign');
            $table->dropForeign('projects_contractor_id_foreign');
            $table->dropForeign('projects_department_id_foreign');
            $table->dropForeign('projects_top_project_id_foreign');
        });

    }
}
