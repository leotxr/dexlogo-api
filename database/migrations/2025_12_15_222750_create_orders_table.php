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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone');
            $table->enum('plano', ['basico', 'pro']);
            $table->decimal('valor', 10, 2);
            $table->string('payment_id')->nullable();
            $table->string('preference_id')->nullable();
            $table->string('external_reference')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'cancelado'])->default('pendente');
            $table->text('detalhes')->nullable();
            $table->timestamps();


            $table->index('email');
            $table->index('status');
            $table->index('telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
