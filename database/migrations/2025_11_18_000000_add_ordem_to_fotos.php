<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdemToFotos extends Migration
{
    public function up()
    {
        Schema::table('fotos', function (Blueprint $table) {
            // coluna nullable para não impactar fotos já existentes
            $table->integer('ordem')->nullable()->after('caminho_original')->index('idx_ordem');
        });
    }

    public function down()
    {
        Schema::table('fotos', function (Blueprint $table) {
            $table->dropIndex('idx_ordem');
            $table->dropColumn('ordem');
        });
    }
}
