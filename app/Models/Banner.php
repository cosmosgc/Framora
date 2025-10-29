<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    public $timestamps = false;

    protected $fillable = [
        'titulo', 'descricao', 'imagem', 'link', 'ordem', 'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function galerias()
    {
        return $this->hasMany(Galeria::class, 'banner_id');
    }
}
