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
        Schema::create('spreadsheet_under_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spread_sheet_id')->constrained('spread_sheets')->cascadeOnDelete();
            $table->dateTime('last_access');
            $table->foreignId('user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spreadsheet_under_observations');
    }
};
