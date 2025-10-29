<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoDestacada extends Model
{
    protected $table = 'fotos_destacadas';
    public $timestamps = false;

    protected $fillable = ['foto_id', 'titulo', 'descricao', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function foto()
    {
        return $this->belongsTo(Foto::class);
    }
}
