<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_materialpeligroso extends Model
{
    use HasFactory;

    protected $table = 'cat_cp_materialpeligroso';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
