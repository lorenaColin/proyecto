<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_colonia extends Model
{
    use HasFactory;

    protected $table = 'cat_colonia';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
