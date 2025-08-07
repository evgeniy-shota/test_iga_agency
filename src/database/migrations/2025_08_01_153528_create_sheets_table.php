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
        Schema::create('sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spread_sheet_id')->constrained('spread_sheets')->cascadeOnDelete();
            $table->string('sheet_id');
            $table->string('title');
            $table->string('range')->nullable();
            $table->smallInteger('row_count')->nullable();
            $table->smallInteger('column_count')->nullable();
            $table->boolean('is_current')->nullable()->default(false);
            $table->boolean('is_initialized')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheets');
    }
};
