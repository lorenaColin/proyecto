<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_tipoPermiso extends Model
{
    use HasFactory;

    protected $table = 'cat_tipopermiso';

    protected $fillable = [
        'id',
        'permission',
        'description',
        'transport'
    ];

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
