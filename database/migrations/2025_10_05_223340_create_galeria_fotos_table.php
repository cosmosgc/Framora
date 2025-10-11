<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('galeria_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('galeria_id')->constrained('galerias')->cascadeOnDelete();
            $table->foreignId('foto_id')->constrained('fotos')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('galeria_fotos');
    }
};
