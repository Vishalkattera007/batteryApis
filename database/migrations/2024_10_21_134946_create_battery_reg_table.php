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
        Schema::create('battery_reg', function (Blueprint $table) {
            $table->id();
            $table->string('serialNo')->nullable();
            $table->string('type')->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('pincode')->nullable();
            $table->string('mobileNumber')->nullable();
            $table->string('BPD')->nullable();
            $table->string('VRN')->nullable();
            $table->string('Acceptance')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_reg');
    }
};
