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
        Schema::create('warehouse_shoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('models_id');
            $table->json('sizes')->nullable();
            $table->integer('residual')->nullable()->comment('Залишок, обчислюєтся автоматично');
            $table->integer('price')->nullable();
            $table->boolean('active')->default(true);

             $table->foreign('group_id')->references('id')->on('group_shoes')->onDelete('cascade');
             $table->foreign('models_id')->references('id')->on('models_shoes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_shoes');
    }
};
