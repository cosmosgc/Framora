<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $table = 'favoritos';
    public $timestamps = false;

    protected $fillable = ['user_id', 'referencia_tipo', 'referencia_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
