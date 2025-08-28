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
        Schema::table('optoms', function (Blueprint $table) {
            $table->string('sale_type')->default('optom');
            $table->string('recorded_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('optoms', function (Blueprint $table) {
            $table->dropColumn(['sale_type', 'recorded_by']);
        });
    }
};
