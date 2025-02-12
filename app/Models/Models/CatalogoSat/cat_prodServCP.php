<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat_prodServCP extends Model
{
    use HasFactory;

    protected $table = 'c_claveprodservcp';

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
