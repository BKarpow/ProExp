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
        Schema::create('image_model_sgoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('models_id');
            $table->json('images')->default('{}');
            $table->boolean('active')->default(true);
            $table->string('desc')->nullable();
            $table->foreign('models_id')->references('id')->on('models_shoes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_model_sgoes');
    }
};
