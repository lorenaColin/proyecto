<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mercancia extends Model
{
    use HasFactory;
    protected $fillable = [
        'claveProdServCP',
        'descripcion',
        'claveUnidad',
        'unidad',
        'dimensiones',
        'materialPeligroso',
        'cveMaterialPeligroso',
        'embalaje',
        'descripEmbalaje',
        'uuid_company',
    ];
}
