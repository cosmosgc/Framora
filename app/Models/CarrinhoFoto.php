<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarrinhoFoto extends Model
{
    protected $table = 'carrinho_fotos';
    public $timestamps = false;

    protected $fillable = [
        'carrinho_id', 'foto_id', 'preco', 'quantidade'
    ];

    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }

    public function foto()
    {
        return $this->belongsTo(Foto::class);
    }
}
