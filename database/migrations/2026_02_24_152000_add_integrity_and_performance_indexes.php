<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'is_read'], 'messages_receiver_read_idx');
            $table->index(['is_general', 'created_at'], 'messages_general_created_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'deadline'], 'tasks_status_deadline_idx');
            $table->index(['assigned_to_id', 'status'], 'tasks_assigned_status_idx');
            $table->index(['created_by_id', 'status'], 'tasks_creator_status_idx');
        });

        Schema::table('vacations', function (Blueprint $table) {
            $table->index(['vacation_user_id', 'is_verified'], 'vacations_user_verify_idx');
            $table->index('vacation_date', 'vacations_date_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'announcements_user_created_idx');
        });

        Schema::table('business_events', function (Blueprint $table) {
            $table->index(['start_date', 'end_date'], 'business_events_start_end_idx');
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->index(['user_id', 'parent_id'], 'folders_user_parent_idx');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->index(['user_id', 'folder_id'], 'files_user_folder_idx');
            $table->index(['folder_id', 'created_at'], 'files_folder_created_idx');
        });

        Schema::table('poll_questions', function (Blueprint $table) {
            $table->index(['poll_id', 'order'], 'poll_questions_poll_order_idx');
        });

        Schema::table('poll_responses', function (Blueprint $table) {
            $table->unique(['poll_id', 'user_id'], 'poll_responses_poll_user_unique');
        });

        Schema::table('poll_answers', function (Blueprint $table) {
            $table->index(['poll_question_id', 'poll_option_id'], 'poll_answers_question_option_idx');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_receiver_read_idx');
            $table->dropIndex('messages_general_created_idx');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_status_deadline_idx');
            $table->dropIndex('tasks_assigned_status_idx');
            $table->dropIndex('tasks_creator_status_idx');
        });

        Schema::table('vacations', function (Blueprint $table) {
            $table->dropIndex('vacations_user_verify_idx');
            $table->dropIndex('vacations_date_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_user_created_idx');
        });

        Schema::table('business_events', function (Blueprint $table) {
            $table->dropIndex('business_events_start_end_idx');
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->dropIndex('folders_user_parent_idx');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex('files_user_folder_idx');
            $table->dropIndex('files_folder_created_idx');
        });

        Schema::table('poll_questions', function (Blueprint $table) {
            $table->dropIndex('poll_questions_poll_order_idx');
        });

        Schema::table('poll_responses', function (Blueprint $table) {
            $table->dropUnique('poll_responses_poll_user_unique');
        });

        Schema::table('poll_answers', function (Blueprint $table) {
            $table->dropIndex('poll_answers_question_option_idx');
        });
    }
};
