<?php

use App\Enums\SpreadSheetRowStatus;
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
        Schema::create('rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('sheets')->cascadeOnDelete();
            $table->integer('row_number');
            $table->enum('status', SpreadSheetRowStatus::getValues());
            $table->string('name')->nullable();
            $table->bigInteger('reserved_count')->nullable()->default(0);
            $table->bigInteger('total_count')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rows');
    }
};
