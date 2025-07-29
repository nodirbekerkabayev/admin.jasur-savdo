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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('karobkadagi_soni');
            $table->integer('necha_karobka_kelgani');
            $table->integer('kelgan_narxi_dona');
            $table->integer('kelgan_narxi_blok');
            $table->integer('sotish_narxi_dona');
            $table->integer('sotish_narxi_blok');
            $table->integer('sotish_narxi_optom_dona');
            $table->integer('sotish_narxi_optom_blok');
            $table->integer('sotish_narxi_toyga_dona');
            $table->integer('sotish_narxi_toyga_blok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
