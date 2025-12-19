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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('email');
            $table->string('telefone', 20);
            $table->enum('plano', ['basico', 'pro'])->default('basico');
            $table->decimal('valor', 10, 2);
            $table->enum('status', ['pendente', 'aprovado', 'cancelado', 'reembolsado'])
                ->default('pendente');
            $table->string('codigo_acesso', 100)->nullable()->unique();
            $table->string('mercadopago_payment_id')->nullable();
            $table->string('mercadopago_preference_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index('codigo_acesso');
            $table->index('mercadopago_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
