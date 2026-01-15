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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->string('table_number');
            $table->integer('capacity')->default(4);
            $table->string('qr_code_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('location')->nullable(); // terrasse, intérieur, étage 1, etc.
            $table->timestamps();

            $table->unique(['establishment_id', 'table_number']);
            $table->index('establishment_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
