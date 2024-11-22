<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //contador
        for($contador=1; $contador<=5; $contador++){
            $invoice1 = new Invoice();
            $invoice1->serie= 'hola';
            $invoice1->date='2024-04-24';
            $invoice1->way_to_pay='gfgs';
            $invoice1->payment_terms='sdfdf';
            $invoice1->subtotal='24.1';
            $invoice1->discount='23.07';
            $invoice1->currency= 'fhghfgjd';
            $invoice1->change_type= '1.2';
            $invoice1->total='12.3';
            $invoice1->invoice_type='rgre';
            $invoice1->export='gerg';
            $invoice1->payment_method='egreg';
            $invoice1->receiver='12';
            $invoice1->invoice_usage='qe';
            $invoice1->uuid='sa';
            $invoice1->timbre_date='2024-04-24';
            $invoice1->cfdi_seal='HGHJD';
            $invoice1->sat_seal='ffsdfsdfds';
            $invoice1->supplier_rfc='fsdfdf';
            $invoice1->save();
        }
    }
}
