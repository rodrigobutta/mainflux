<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('todos', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('login_as_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('profiles', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::table('messages', function(Blueprint $table)
        {
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('messages')->onDelete('cascade');
        });

        Schema::table('user_preferences', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('uploads', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('designations', function(Blueprint $table)
        {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('top_designation_id')->references('id')->on('designations')->onDelete('cascade');
        });

        Schema::table('locations', function(Blueprint $table)
        {
            $table->foreign('top_location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::table('questions', function(Blueprint $table)
        {
            $table->foreign('question_set_id')->references('id')->on('question_sets')->onDelete('cascade');
        });

        Schema::table('tasks', function(Blueprint $table)
        {
            $table->foreign('task_category_id')->references('id')->on('task_categories')->onDelete('cascade');
            $table->foreign('task_priority_id')->references('id')->on('task_priorities')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recurring_task_id')->references('id')->on('tasks')->onDelete('set null');
            $table->foreign('question_set_id')->references('id')->on('question_sets')->onDelete('cascade');
        });

        Schema::table('answers', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

        Schema::table('sub_tasks', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('task_user', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('starred_tasks', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('announcements', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('announcement_designation', function(Blueprint $table)
        {
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
        });

        Schema::table('announcement_location', function(Blueprint $table)
        {
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::table('announcement_user', function(Blueprint $table)
        {
            $table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('task_comments', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('task_comments')->onDelete('cascade');
        });

        Schema::table('task_notes', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('task_attachments', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('task_signoff_logs', function(Blueprint $table)
        {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('sub_task_ratings', function(Blueprint $table)
        {
            $table->foreign('sub_task_id')->references('id')->on('sub_tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->dropForeign('backups_user_id_foreign');
        });

        Schema::table('todos', function(Blueprint $table)
        {
            $table->dropForeign('todos_user_id_foreign');
        });

        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->dropForeign('activity_logs_user_id_foreign');
            $table->dropForeign('activity_logs_login_as_user_id_foreign');
        });

        Schema::table('profiles', function(Blueprint $table)
        {
            $table->dropForeign('profiles_user_id_foreign');
            $table->dropForeign('profiles_designation_id_foreign');
            $table->dropForeign('profiles_location_id_foreign');
        });

        Schema::table('messages', function(Blueprint $table)
        {
            $table->dropForeign('messages_from_user_id_foreign');
            $table->dropForeign('messages_to_user_id_foreign');
            $table->dropForeign('messages_reply_id_foreign');
        });

        Schema::table('user_preferences', function(Blueprint $table)
        {
            $table->dropForeign('user_preferences_user_id_foreign');
        });

        Schema::table('uploads', function(Blueprint $table)
        {
            $table->dropForeign('uploads_user_id_foreign');
        });

        Schema::table('designations', function(Blueprint $table)
        {
            $table->dropForeign('designations_department_id_foreign');
            $table->dropForeign('designations_top_designation_id_foreign');
        });

        Schema::table('locations', function(Blueprint $table)
        {
            $table->dropForeign('locations_top_location_id_foreign');
        });

        Schema::table('questions', function(Blueprint $table)
        {
            $table->dropForeign('questions_question_set_id_foreign');
        });

        Schema::table('tasks', function(Blueprint $table)
        {
            $table->dropForeign('tasks_task_category_id_foreign');
            $table->dropForeign('tasks_task_priority_id_foreign');
            $table->dropForeign('tasks_user_id_foreign');
            $table->dropForeign('tasks_recurring_task_id_foreign');
            $table->dropForeign('tasks_question_set_id_foreign');
        });

        Schema::table('answers', function(Blueprint $table)
        {
            $table->dropForeign('answers_task_id_foreign');
            $table->dropForeign('answers_question_id_foreign');
        });

        Schema::table('sub_tasks', function(Blueprint $table)
        {
            $table->dropForeign('sub_tasks_task_id_foreign');
            $table->dropForeign('sub_tasks_user_id_foreign');
        });

        Schema::table('task_user', function(Blueprint $table)
        {
            $table->dropForeign('task_user_task_id_foreign');
            $table->dropForeign('task_user_user_id_foreign');
        });

        Schema::table('starred_tasks', function(Blueprint $table)
        {
            $table->dropForeign('starred_task_task_id_foreign');
            $table->dropForeign('starred_task_user_id_foreign');
        });

        Schema::table('announcements', function(Blueprint $table)
        {
            $table->dropForeign('announcements_user_id_foreign');
        });

        Schema::table('announcement_designation', function(Blueprint $table)
        {
            $table->dropForeign('announcement_designation_announcement_id_foreign');
            $table->dropForeign('announcement_designation_designation_id_foreign');
        });

        Schema::table('announcement_location', function(Blueprint $table)
        {
            $table->dropForeign('announcement_location_announcement_id_foreign');
            $table->dropForeign('announcement_location_location_id_foreign');
        });

        Schema::table('announcement_user', function(Blueprint $table)
        {
            $table->dropForeign('announcement_user_announcement_id_foreign');
            $table->dropForeign('announcement_user_user_id_foreign');
        });

        Schema::table('task_comments', function(Blueprint $table)
        {
            $table->dropForeign('task_comments_task_id_foreign');
            $table->dropForeign('task_comments_user_id');
            $table->dropForeign('task_comments_reply_id');
        });

        Schema::table('task_notes', function(Blueprint $table)
        {
            $table->dropForeign('task_notes_task_id_foreign');
            $table->dropForeign('task_notes_user_id_foreign');
        });

        Schema::table('task_attachments', function(Blueprint $table)
        {
            $table->dropForeign('task_attachments_task_id_foreign');
            $table->dropForeign('task_attachments_user_id_foreign');
        });

        Schema::table('task_signoff_logs', function(Blueprint $table)
        {
            $table->dropForeign('task_signoff_logs_task_id_foreign');
            $table->dropForeign('task_signoff_logs_user_id_foreign');
        });

        Schema::table('sub_task_ratings', function(Blueprint $table)
        {
            $table->dropForeign('sub_task_ratings_sub_task_id_foreign');
            $table->dropForeign('sub_task_ratings_user_id_foreign');
        });
    }
}
