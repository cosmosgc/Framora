<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\User;
use App\Models\Banner;
use App\Models\Foto;

class ImageSetting extends Model
{
    protected $table = 'galerias';

    protected $fillable = ['key', 'value'];
    public $timestamps = true;
}
