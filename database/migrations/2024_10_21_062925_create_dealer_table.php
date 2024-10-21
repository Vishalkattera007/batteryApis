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
        Schema::create('dealer', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number');
            $table->string('address');
            $table->string('adhar');
            $table->string('profileImage');
            $table->timestamp('created_on')->useCurrent(); // For the 'Created On' column
            $table->string('created_by'); // For the 'Created By' column
            $table->timestamp('updated_on')->nullable(); // For the 'Updated On' column, nullable in case no updates are made
            $table->string('updated_by')->nullable(); // For the 'Updated By' column, also nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer');
    }
};
