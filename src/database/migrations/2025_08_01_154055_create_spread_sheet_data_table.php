<?php

use App\Enums\SpreadSheetLineStatus;
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
        Schema::create('spread_sheet_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spread_sheets_id')->constrained('spread_sheets')->cascadeOnDelete();
            $table->enum('status', SpreadSheetLineStatus::getValues());
            $table->json('columns')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spread_sheet_data');
    }
};
