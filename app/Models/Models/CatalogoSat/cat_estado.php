<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_estado extends Model
{
    use HasFactory;

    protected $table = 'cat_estado';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
