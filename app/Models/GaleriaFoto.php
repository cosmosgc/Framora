<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriaFoto extends Model
{
    protected $table = 'galeria_fotos';
    public $timestamps = false;

    protected $fillable = ['galeria_id', 'foto_id'];

    public function galeria()
    {
        return $this->belongsTo(Galeria::class);
    }

    public function foto()
    {
        return $this->belongsTo(Foto::class);
    }
}
