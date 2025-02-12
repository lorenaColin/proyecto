<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_cp_remolque extends Model
{
    use HasFactory;

    protected $table = 'cat_cp_remolque';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
