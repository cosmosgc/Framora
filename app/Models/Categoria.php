<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    public $timestamps = false;

    protected $fillable = ['nome', 'descricao', 'thumbnail', 'banner_id'];

    public function galerias()
    {
        return $this->hasMany(Galeria::class);
    }

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
