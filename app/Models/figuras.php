<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class figuras extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipoFigura',
        'rfcFigura',
        'numLicencia',
        'nombreFigura',
        'numRegIdTribFigura',
        'residenciaFiscalFigura',
        'domicilio',
        'pais',
        'codigoPostal',
        'estado',
        'municipio',
        'localidad',
        'colonia',
        'calle',
        'numeroExterior',
        'numeroInterior',
        'referencia',
        'uuid_company',
    ];
}
