<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['household_id', 'expense_date'], 'expenses_household_date_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('household_id', 'categories_household_idx');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index(['household_id', 'month'], 'budgets_household_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_household_date_idx');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_household_idx');
        });
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex('budgets_household_month_idx');
        });
    }
};
