<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('descricao');
            $table->foreignId('banner_id')
                ->nullable()
                ->constrained('banners')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('banner_id');
            $table->dropColumn('thumbnail');
        });
    }
};
