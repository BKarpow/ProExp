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
        Schema::table('date_products', function (Blueprint $table) {
            $table->boolean('done')
                ->default(false)
                ->after('end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('date_products', function (Blueprint $table) {
            $table->dropColumn('done');
        });
    }
};
