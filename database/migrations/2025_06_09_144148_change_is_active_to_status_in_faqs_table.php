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
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('category_id');
        });
    }
};
