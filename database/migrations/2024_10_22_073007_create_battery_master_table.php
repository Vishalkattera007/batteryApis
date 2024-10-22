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
        Schema::create('battery_master', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no')->unique()->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('MFD')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_master');
    }
};
