<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Providers\Cfdi;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $hora = date("H:i:s");
        $fecha = "2025-01-16"."T".$hora;
        $noCertificado = "30001000000500003416";
        $certificado = "MIIFsDCCA5igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MTYwDQYJKoZIhvcNAQELBQ";

        $subtotal = 0;
        $moneda = "XXX";
        $total = 0;
        $lugarExpedicion = 42501;
        $exportacion = "01";
        
        $version = "4.0";

        $tipoComprobante = "P";

        
        switch ($tipoComprobante) {
            case 'P':
                $usoCfdi = "CP01";
                $formaPago = "";
                $condicionesPago = "";
                $metodoPago = "";
                $descuento = 0;
                $tipoCambio = "";
                $serie = "";
                $folio = "";

                $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago", "claveProdServ":"84111506", "claveUnidad":"ACT", "unidad":"", "valorUnitario":"0.000000", "objetoImp":"01", "importe":"0.000000", "descuento": "0.000000", "noIdentificacion": ""}]', true);
                break;
            case 'N':
                $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago de nómina", "claveProdServ":"84111505", "claveUnidad":"ACT", "valorUnitario": "0.000000", "descuento":"0.00", "objetoImp":"01", "importe":"0.000000"}]', true);
                break;
            default:
                $listaConceptos = json_decode($conceptosJson, true);
                break;
        }
        // $descuento = 0;
        $xml = new Cfdi($version, $fecha, $noCertificado, $certificado, $subtotal, $moneda, $total, $tipoComprobante, $lugarExpedicion, $exportacion, $formaPago, $condicionesPago, $metodoPago, $descuento, $tipoCambio, $serie, $folio);


        $rfcEmisor = "EKU9003173C9";
        $nombreEmisor = "ESCUELA KEMPER URGATE";
        $regimenFiscalEmisor = "601";

        $xml->setEmisor($rfcEmisor, $nombreEmisor, $regimenFiscalEmisor);

        $rfcReceptor = "EKU9003173C9";
        $nombreReceptor = "ESCUELA KEMPER URGATE";
        $domicilioReceptor = 42501;
        $regimenFiscalReceptor = 601;
        $residenciaFiscal = "";
        $numRegIdTrib = "";

        $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        $xml->setConceptos($listaConceptos);

        // $xml->setPago20();
        $xml->setCartaPorte31();
        $xml->saveCfdi();

        return response()->json(['message' => "creado"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
