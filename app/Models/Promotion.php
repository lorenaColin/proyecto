<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';

    protected $fillable = [
        'date_init',
        'date_end',
        'new_quantity',
        'new_percentaje',
        'current_quantity',
        'current_percentaje',
        'type',
        'status',
    ];

}
