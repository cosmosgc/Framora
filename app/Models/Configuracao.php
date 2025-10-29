<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';
    public $timestamps = false;

    protected $fillable = ['chave', 'valor'];
}
