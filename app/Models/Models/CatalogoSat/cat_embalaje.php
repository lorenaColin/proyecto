<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_embalaje extends Model
{
    use HasFactory;

    protected $table = 'c_cp_tipoembalajes';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
