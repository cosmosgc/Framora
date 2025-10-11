<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('referencia_tipo', ['foto', 'galeria', 'evento']);
            $table->unsignedBigInteger('referencia_id');
            $table->timestamp('criado_em')->useCurrent();

            $table->unique(['user_id', 'referencia_tipo', 'referencia_id'], 'uniq_favorito');
        });
    }

    public function down(): void {
        Schema::dropIfExists('favoritos');
    }
};
