<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table = 'fotos';
    public $timestamps = false;

    protected $fillable = [
        'referencia_tipo', 'galeria_id',
        'caminho_thumb', 'caminho_foto', 'caminho_original', 'ativo'
    ];

    protected $casts = ['ativo' => 'boolean'];

    public function destacada()
    {
        return $this->hasOne(FotoDestacada::class);
    }

    public function carrinhoFotos()
    {
        return $this->hasMany(CarrinhoFoto::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }    
    public function getUrlThumbAttribute()
    {
        return asset($this->caminho_thumb);
    }

    public function getUrlFotoAttribute()
    {
        return asset($this->caminho_foto);
    }

    public function getUrlOriginalAttribute()
    {
        return asset($this->caminho_original);
    }
}
