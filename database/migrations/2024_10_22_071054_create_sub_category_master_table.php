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
    Schema::create('sub_category_master', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('categoryId')->nullable();  // Use unsignedBigInteger for foreign key
        $table->string('sub_category_name');
        $table->string('created_by')->nullable();
        $table->string('updated_by')->nullable();
        $table->timestamps();

        // Add foreign key constraint
        $table->foreign('categoryId')->references('id')->on('category_master')->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_category_master');
    }
};
