<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('galerias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('banner_id')->nullable()->constrained('banners')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            
            $table->string('nome')->nullable();
            $table->text('descricao')->nullable();
            $table->string('local')->nullable();
            $table->date('data')->nullable();
            $table->string('tempo_duracao')->nullable();
            $table->decimal('valor_foto', 10, 2)->default(0.00);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('galerias');
    }
};
