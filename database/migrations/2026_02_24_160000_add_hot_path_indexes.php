<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(
                ['notifiable_type', 'notifiable_id', 'read_at', 'created_at'],
                'notifications_notifiable_read_created_idx'
            );
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->index(['is_active', 'start_date', 'end_date'], 'polls_active_window_idx');
            $table->index('created_at', 'polls_created_at_idx');
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->index(['user_id', 'role_id'], 'role_user_user_role_idx');
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->index(['role_id', 'permission_id'], 'permission_role_role_permission_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_created_idx');
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex('polls_active_window_idx');
            $table->dropIndex('polls_created_at_idx');
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropIndex('role_user_user_role_idx');
        });

        Schema::table('permission_role', function (Blueprint $table) {
            $table->dropIndex('permission_role_role_permission_idx');
        });
    }
};
