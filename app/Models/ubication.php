<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ubication extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfc',
        'idUbicacion',
        'NombreRemitenteDestinatario',
        'numRegIdTrib',
        'residenciaFiscal',
        'tipoUbicacion',
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
