<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->string('name')->nullable();
            $table->integer('task_relevance_id')->unsigned();
            $table->integer('task_complexity_id')->unsigned();
            $table->integer('task_frequency_id')->unsigned();
            $table->integer('task_family_id')->unsigned();  
            $table->boolean('is_hidden')->default(0);                      
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
        Schema::dropIfExists('tasks');
    }
}
