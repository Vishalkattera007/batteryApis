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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // For the 'Name' column
            $table->string('email')->unique(); // For the 'Email' column, with unique constraint
            $table->string('password'); // For the 'Password' column
            $table->string('phone_number'); // For the 'PhoneNumber' column
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
        Schema::dropIfExists('admin');
    }
};
