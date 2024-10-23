<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('battery_master', function (Blueprint $table) {
            // Make sure category and sub_category are integers
            $table->unsignedBigInteger('category')->change(); // Adjust the data type if necessary
            $table->unsignedBigInteger('sub_category')->change();

            // Add foreign key constraints
            $table->foreign('category')->references('id')->on('category_master')->onDelete('cascade');
            $table->foreign('sub_category')->references('id')->on('sub_category_master')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('battery_master', function (Blueprint $table) {
            // Drop foreign key constraints if rolling back
            $table->dropForeign(['category']);
            $table->dropForeign(['sub_category']);
        });
    }
};
