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
        //
        Schema::table('battery_reg', function(Blueprint $table){
            $table->string('email')->unique()->change();
            $table->string('mobileNumber')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battery_reg', function(Blueprint $table){
            $table->string('email')->dropUnique()->change();
            $table->string('mobileNumber')->dropUnique()->change();
        });
    }
};
