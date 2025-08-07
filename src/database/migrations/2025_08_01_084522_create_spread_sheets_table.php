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
        Schema::create('spread_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('spreadsheet_id')->index();
            $table->string('title');
            $table->string('url');
            $table->string('range')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spread_sheets');
    }
};
