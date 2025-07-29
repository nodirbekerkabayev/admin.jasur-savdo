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
        Schema::create('firms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('supervisor');
            $table->string('s_phone')->nullable();
            $table->string('agent');
            $table->string('a_phone')->nullable();
            $table->string('currier');
            $table->string('c_phone')->nullable();
            $table->boolean('humo');
            $table->boolean('uzcard');
            $table->string('day');
            $table->string('debt');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firms');
    }
};
