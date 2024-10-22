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
        Schema::create('assigned_batteries', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_id')->nullable();
            $table->string('catergory_id')->nullable();
            $table->string('sub_category_id')->nullable();
            $table->string('nof_batteries')->nullable();
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
        Schema::dropIfExists('assigned_batteries');
    }
};
