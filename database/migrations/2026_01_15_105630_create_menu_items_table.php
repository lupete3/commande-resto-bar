<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('menu_categories')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('CDF'); // CDF, USD, EUR
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('preparation_time')->default(15); // minutes
            $table->json('allergens')->nullable(); // ["gluten", "lactose", "nuts"]
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('establishment_id');
            $table->index('category_id');
            $table->index(['establishment_id', 'is_available']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
