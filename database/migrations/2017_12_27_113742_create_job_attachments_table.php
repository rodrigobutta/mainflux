<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_attachments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('job_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('job_attachments');
    }
}
