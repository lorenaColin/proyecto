<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_localidad extends Model
{
    use HasFactory;

    protected $table = 'cat_localidad';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
