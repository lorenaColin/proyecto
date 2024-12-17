<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class catPostCode extends Model
{
    use HasFactory;

    protected $table = 'cat_cp';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
