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
            $table->uuid('uuid')->nullable();
            $table->integer('question_set_id')->unsigned()->nullable();
            $table->integer('task_category_id')->unsigned()->nullable();
            $table->integer('task_priority_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('progress')->default(0);
            $table->string('progress_type',20)->nullable();
            $table->string('rating_type',20)->nullable();
            $table->string('sign_off_status',20)->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->boolean('is_cancelled')->default(0);
            $table->boolean('is_archived')->default(0);
            $table->boolean('is_recurring')->default(0);
            $table->date('recurrence_start_date')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->integer('recurring_frequency')->default(0);
            $table->date('next_recurring_date')->nullable();
            $table->integer('recurring_task_id')->unsigned()->nullable();
            $table->string('upload_token')->nullable();
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
