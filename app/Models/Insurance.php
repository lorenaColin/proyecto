<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $table = 'insurances';
    protected $fillable = [
        'type',
        'asegure',
        'polize',
        'company_id'
    ];

    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
