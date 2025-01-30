<?php

namespace App\Models\Models\CatalogoSat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cat_configVehiculo extends Model
{
    use HasFactory;

    protected $table = 'cat_configautotransporte';

    protected $fillable = [
        'nomenclature',
        'description',
        'n_ejes',
        'n_llantas',
        'remolq'
    ];

    public $timestamps = false;

    protected $connection = 'mysql_cat'; 
}
