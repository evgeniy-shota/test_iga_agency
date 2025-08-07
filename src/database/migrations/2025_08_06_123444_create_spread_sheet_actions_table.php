<?php

use App\Enums\SpreadsheetActionStatus;
use App\Enums\SpreadsheetActionType;
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
        Schema::create('spread_sheet_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spread_sheet_id')->constrained('spread_sheets')
                ->cascadeOnDelete();
            $table->enum('action_type', SpreadsheetActionType::getValues());
            $table->enum('action_status', SpreadsheetActionStatus::getValues())
                ->default(SpreadsheetActionStatus::Awaits->value)->nullable();
            $table->json('action_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spread_sheet_actions');
    }
};
