<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'customers';
    protected $fillable = [
        'rfc',
        'name',
        'cp',
        'residence',
        'num_reg_id_trib',
        'regime',
        'address',
        'email',
        'phone',
        'status',
        'company_id',
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
