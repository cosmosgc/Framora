<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'user_id', 'carrinho_id', 'status_pedido', 'forma_pagamento', 'valor_total'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }

    public function inventario()
    {
        return $this->hasMany(Inventario::class);
    }
}
