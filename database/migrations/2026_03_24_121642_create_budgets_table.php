<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ho an’ny utilisateur
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // budget par catégorie
            $table->decimal('amount', 10, 2); // montant budget
            $table->date('month'); // mois du budget
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
};
