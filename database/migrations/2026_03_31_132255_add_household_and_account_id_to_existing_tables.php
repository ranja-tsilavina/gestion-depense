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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'household_id')) {
                $table->foreignId('household_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
        Schema::table('budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('budgets', 'household_id')) {
                $table->foreignId('household_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'household_id')) {
                $table->foreignId('household_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('expenses', 'account_id')) {
                $table->foreignId('account_id')->nullable()->constrained()->onDelete('set null');
            }
        });
        Schema::table('revenues', function (Blueprint $table) {
            if (!Schema::hasColumn('revenues', 'household_id')) {
                $table->foreignId('household_id')->nullable()->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('revenues', 'account_id')) {
                $table->foreignId('account_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) { if (Schema::hasColumn('categories', 'household_id')) $table->dropColumn('household_id'); });
        Schema::table('budgets', function (Blueprint $table) { if (Schema::hasColumn('budgets', 'household_id')) $table->dropColumn('household_id'); });
        Schema::table('expenses', function (Blueprint $table) { 
            if (Schema::hasColumn('expenses', 'household_id')) $table->dropColumn('household_id'); 
            if (Schema::hasColumn('expenses', 'account_id')) $table->dropColumn('account_id'); 
        });
        Schema::table('revenues', function (Blueprint $table) { 
            if (Schema::hasColumn('revenues', 'household_id')) $table->dropColumn('household_id'); 
            if (Schema::hasColumn('revenues', 'account_id')) $table->dropColumn('account_id'); 
        });
    }
};
