<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fotos', function (Blueprint $table) {
            $table->id();
            $table->enum('referencia_tipo', ['galeria', 'evento']);
            $table->unsignedInteger('referencia_id');
            $table->string('caminho_thumb')->nullable();
            $table->string('caminho_foto')->nullable();
            $table->string('caminho_original')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();

            $table->index(['referencia_tipo', 'referencia_id'], 'idx_ref');
        });
    }

    public function down(): void {
        Schema::dropIfExists('fotos');
    }
};
