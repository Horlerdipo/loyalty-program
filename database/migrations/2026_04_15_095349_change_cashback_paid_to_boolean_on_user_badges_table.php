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
        Schema::table('user_badges', function (Blueprint $table) {
            $table->boolean('cashback_paid')->default(false)->change();
            $table->decimal('cashback_amount')->after('cashback_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boolean_on_user_badges', function (Blueprint $table) {
            //
        });
    }
};
