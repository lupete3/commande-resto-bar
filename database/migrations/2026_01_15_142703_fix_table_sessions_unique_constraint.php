<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('table_sessions', function (Blueprint $table) {
            $table->dropUnique('unique_active_session');
            $table->index(['table_id', 'is_active'], 'idx_table_active_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_table_active_session');
            $table->unique(['table_id', 'is_active'], 'unique_active_session');
        });
    }
};
