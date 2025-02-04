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
        'cp',
        'curp',
        'status',
        'rfc',
        'regime',
        'employee_registration',
        'logo',
        'type',
        'email',
        'phone',
        'id_usr_create'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'collaborator_company', 'company_id', 'collaborator_id')
            ->withPivot('permiso') 
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'id_usr_create'); 
    }
    public function user(){
        return $this->belongsTo(User::class, 'id_usr_create');
    }

    public function companyDetail()
    {
        return $this->hasOne('App\Models\CompanyDetail');
    }
    public function payments()
    {
        return $this->hasMany('App\Models\Payment', 'company_id');
    }
    
    public function autotransports()
    {
        return $this->hasMany('App\Models\Autotransport', 'company_id');
    }

    public function insurances()
    {
        return $this->hasMany('App\Models\Insurance', 'company_id');
    }
}
