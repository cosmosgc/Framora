<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Categoria;

class Galeria extends Model
{
    protected $table = 'galerias';
    public $timestamps = false;

    protected $fillable = [
        'categoria_id', 'banner_id', 'nome', 'descricao', 'local',
        'data', 'tempo_duracao', 'valor_foto'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public function fotos()
    {
        return $this->belongsToMany(Foto::class, 'galeria_fotos');
    }
}
