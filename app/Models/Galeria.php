<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\User;
use App\Models\Banner;
use App\Models\Foto;

class Galeria extends Model
{
    protected $table = 'galerias';
    public $timestamps = false;

    protected $fillable = [
        'categoria_id',
        'banner_id',
        'user_id',
        'nome',
        'descricao',
        'local',
        'data',
        'tempo_duracao',
        'valor_foto',
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
        return $this->hasMany(Foto::class, 'galeria_id')
        ->orderBy('ordem')
        ->orderBy('id'); // opcional, fallback;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
