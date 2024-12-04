<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'companies';
    protected $fillable = [
        'name',
        'address',
        'colony',
        'municipality',
        'cp',
        'curp',
        'status',
        'rfc',
        'state',
        'locality',
        'regime',
        'employee_registration',
        'logo',
        'type',
        'email',
        'phone',
        'id_usr_create'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
