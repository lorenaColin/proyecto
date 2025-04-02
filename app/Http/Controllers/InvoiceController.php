<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\Cfdi;
use App\Models\Company;
use App\Models\Customer;
use App\Http\Responses\ApiResponse;
use nusoap_client;
use Illuminate\Support\Facades\Storage;
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
        // return response()->json(['message' => $request->uuid_company]);
        $tipoComprobante = $request->invoice_type;
        $folio = "";
        $serie = $request->serie_folio;
        $hora = date("H:i:s");
        $fecha = $request->fecha."T".$hora;

        $usoCfdi = $request->uso_cfdi;
        $metodoPago = $request->metodo_pago;
        $formaPago = $request->forma_pago;
        $moneda = $request->moneda;
        $tipoCambio = $request->tipoCambio;
        
        $subtotal = $request->subtotal;
        $total = $request->total;
        $descuento = $request->descuento;
        $retenciones = $request->retenciones;

        $condicionesPago = $request->condiciones;


        $traslados = $request->traslados;
        $relaciones = $request->relaciones;
        $concepts = $request->concepts;

        $uuidCompany = $request->uuid_company;



        $empresa = Company::select('companies.name', 'companies.rfc', 'companies.cp', 'companies.regime', 'company_details.fechaven as vencimiento', 'company_details.tones_incluide as timbres', 'company_details.expiration_date_cert as vencimientoCertificado', 'company_details.certificate as noCertificado', 'company_details.contcert as certificado' )
            ->join('company_details', 'company_details.company_id', '=', 'companies.id')
            ->where('companies.id', '=', $uuidCompany)
            ->get();

        $rfcEmisor = $empresa[0]->name;
        $nombreEmisor = $empresa[0]->rfc;
        $cpEmisor =$empresa[0]->cp;
        $regimenFiscalEmisor = $empresa[0]->regime;
        $fechaVencimiento = $empresa[0]->vencimiento;


        if (strtotime(date("Y-m-d")) > strtotime($fechaVencimiento)) {
            return ApiResponse::error('Error', 404, 'Cuenta vencida');
        }
        
        $timbres = $empresa[0]->timbres;
        if(intval($timbres) == 0){
            return ApiResponse::error('Error', 404, 'Sin timbres');
        }
        
        $noCertificado = $empresa[0]->noCertificado;
        $certificado = $empresa[0]->certificado;

        // if(!isset($noCertificado)){
        //     return ApiResponse::error('Error', 404, 'No haz subido tus sellos CSD');
        // }

        // $vencimientoCertificado = substr($empresa[0]->vencimientoCertificado, 0, 19);
        // return ApiResponse::error('Error', 404, $vencimientoCertificado);

        $idCliente = 'f0557162-420d-4f8e-9298-a40455ab8651';
        $cliente = Customer::select('name', 'rfc', 'cp', 'regime', 'residence', 'num_reg_id_trib')->where('id', '=', $idCliente)->where('company_id', '=', $uuidCompany)->get();
        
        $nombreReceptor = $cliente[0]->name;
        $rfcReceptor = $cliente[0]->rfc;
        $domicilioReceptor = $cliente[0]->cp;
        $regimenFiscalReceptor = $cliente[0]->regime;
        $residenciaFiscal = $cliente[0]->residence;
        $numRegIdTrib = $cliente[0]->num_reg_id_trib;

        $exportacion = "01";
        
        $version = "4.0";

        switch ($tipoComprobante) {
            case 'P':
                $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago", "claveProdServ":"84111506", "claveUnidad":"ACT", "unidad":"", "valorUnitario":"0.000000", "objetoImp":"01", "importe":"0.000000", "descuento": "0.000000", "noIdentificacion": ""}]', true);
                break;
            case 'N':
                $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago de nómina", "claveProdServ":"84111505", "claveUnidad":"ACT", "valorUnitario": "0.000000", "descuento":"0.00", "objetoImp":"01", "importe":"0.000000"}]', true);
                break;
            default:
                $conceptos = $concepts;
                switch ($tipoComprobante) {
                    case 'E':
                        $comprobante = 'egreso';
                        break;
                    case 'T':
                        $comprobante = 'traslado';
                        break;
                    default:
                        $comprobante = 'ingreso';
                        break;
                }
                break;
        }
        // return response()->json(['message' => count($relaciones)]);
        $directorio = storage_path().'/app/public/companies/'.$uuidCompany.'/comprobantes/'.$comprobante.'/';
        $xml = new Cfdi($version, $fecha, $noCertificado, $certificado, $subtotal, $moneda, $total, $tipoComprobante, $cpEmisor, $exportacion, $formaPago, $condicionesPago, $metodoPago, $descuento, $tipoCambio, $serie, $folio, $directorio);

        $xml->setEmisor($rfcEmisor, $nombreEmisor, $regimenFiscalEmisor);
        $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        
        if(count($relaciones) > 0){
            $xml->setUuidsRelacionados($relaciones);
        }
        $xml->setConceptos($conceptos);

    
        // $descuento = 0;
        


        // $rfcEmisor = "EKU9003173C9";
        // $nombreEmisor = "ESCUELA KEMPER URGATE";
        // $regimenFiscalEmisor = "601";

       

        

        // $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        // $xml->setConceptos($listaConceptos);

        // // $xml->setPago20();
        // $xml->setCartaPorte31();
        $xml->saveCfdi();
        // sleep(7);
        return response()->json(['message' => $directorio]);
        // $metodo = "timbradoBase64Prueba";

        // $data = array('contrato' => '97de57eb-d7f0-436e-af88-0b6d18783ad4', 'usuario' => 'adrian.rebollar@easysweb.com.mx', 'passwd' => 'C1nt3gr@n', 'cfdiXmlBase64' => base64_encode(file_get_contents($directorio."generica.xml")));
        // $client = new nusoap_client('https://timbrado.pade.mx/servicio/Timbrado4.0?wsdl',true);
        // $client->soap_defencoding = 'UTF-8';
        // $client->decode_utf8 = FALSE;

        // $result = $client->call($metodo, $data);
        // $respuesta = new \SimpleXMLElement($result['return']);

        // return response()->json(['message' => $respuesta]);
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
