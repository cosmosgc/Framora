<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fotos', function (Blueprint $table) {
            // Add the galeria_id FK (nullable if needed)
            $table->unsignedBigInteger('galeria_id')->nullable()->after('id');
            $table->foreign('galeria_id')->references('id')->on('galerias')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('fotos', function (Blueprint $table) {
            $table->dropForeign(['galeria_id']);
            $table->dropColumn('galeria_id');
        });
    }
};
