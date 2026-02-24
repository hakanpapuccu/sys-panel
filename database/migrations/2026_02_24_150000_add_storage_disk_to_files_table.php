<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('files', 'storage_disk')) {
            Schema::table('files', function (Blueprint $table) {
                $table->string('storage_disk', 32)->default('public')->after('file_path');
                $table->index('storage_disk');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('files', 'storage_disk')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropIndex(['storage_disk']);
                $table->dropColumn('storage_disk');
            });
        }
    }
};
