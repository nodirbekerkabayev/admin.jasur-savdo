<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name')->nullable();
            $table->integer('quantity');
            $table->string('unit')->default('dona');
            $table->integer('price');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
};
