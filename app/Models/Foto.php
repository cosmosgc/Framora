<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table = 'fotos';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'referencia_tipo',
        'caminho_thumb',
        'caminho_foto',
        'caminho_original',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

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
    public function galeria()
    {
        // ajuste a chave caso sua FK seja diferente (ex: referencia_id)
        return $this->belongsTo(Galeria::class, 'galeria_id');
    }
}
