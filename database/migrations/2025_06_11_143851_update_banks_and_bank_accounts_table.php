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
        Schema::table('banks', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('is_deleted');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->unique(['user_id', 'bank_id'], 'unique_user_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop unique constraint dari tabel bank_accounts
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropUnique('unique_user_bank');
        });
    }
};
