<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        
        'product_key',
        'unit',
        'unit_description',
        'unit_price',
        'quantity',
        'status',
        'identifier_number',
        'internal_key',
        'description',
        'uuid_company',
    ];
    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'uuid_company');
    }
}
