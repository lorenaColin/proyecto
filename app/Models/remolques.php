<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class remolques extends Model
{
use HasFactory;
protected $fillable = [
    'SubTipoRem',
    'placa',
    'uuid_company'
];
}
