<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = [
        'serie',
        'folio',
        'tipoComprobante',
        'uuid_company',
        'status',
    ];
}
