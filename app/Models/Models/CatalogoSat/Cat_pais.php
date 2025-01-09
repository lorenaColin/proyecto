<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat_pais extends Model
{
    use HasFactory;

    protected $table = 'catalogo_pais';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
