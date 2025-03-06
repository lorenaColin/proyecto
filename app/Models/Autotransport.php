<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autotransport extends Model
{
    use HasFactory;

    protected $table = 'autotransports';
    protected $fillable = [
        'configVehicular',
        'pesoBrutoVehicular',
        'placaVM',
        'anioModeloVM',
        'permSCT',
        'numPermisoSCT',
        'aseguraRespCivil',
        'polizaRespCivil',
        'tipoRemolque',
        'company_id'
    ];

    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
