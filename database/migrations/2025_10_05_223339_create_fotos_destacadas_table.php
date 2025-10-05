<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fotos_destacadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foto_id')->constrained('fotos')->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fotos_destacadas');
    }
};
