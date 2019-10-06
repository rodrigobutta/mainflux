<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_notes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('job_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(0);
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
        Schema::dropIfExists('job_notes');
    }
}
