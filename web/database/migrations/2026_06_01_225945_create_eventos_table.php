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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data');
            $table->time('horario');
            $table->string('local');
            $table->unsignedInteger('quantidade_vagas');
            $table->unsignedInteger('vagas_disponiveis');
            $table->string('status')->default('aberto');
            $table->timestamps();

            $table->index('status');
            $table->index('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
