<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'type',
        'description',
        'tones',
        'gitf_tones',
        'amount',
        'date',
        'company_id',
    ];
    public function customerPayment()
    {
        return $this->belongsTo('App\Models\company', 'company_id');
    }
}


