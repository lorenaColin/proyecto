<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat_claveUnidad extends Model
{
    use HasFactory;

    protected $table = 'cat_claveunidad';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
