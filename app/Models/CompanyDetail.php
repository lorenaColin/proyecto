<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $table = 'company_details';

    protected $fillable = [
        'tones_incluide',
        'pac_id',
        'fechaco',
        'fechaven',
        'sta_prod',
        'certificate',
        'private_key',
        'password_key',
        'contcert',
        'expiration_date_cert',
        'start_date_cert',
        'company_id'
    ];
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
