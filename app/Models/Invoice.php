<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';

    protected $fillable = [
        'serie',
        'date',
        'way_to_pay',
        'payment_method',
        'payment_conditions',
        'subtotal',
        'discount',
        'currency',
        'change_type',
        'folio',
        'payment_terms',
        'total',
        'export',
        'invoice_type',
        'type_receipt',
        'invoice_usage',
        'uuid',
        'timbre_date',
        'cfdi_seal',
        'sat_seal',
        'rfc_pac',
        'receiver_id',
        'creation_date',
        'type_relation',
        'uuid_company',
        'xml_filename',
    ];

}
