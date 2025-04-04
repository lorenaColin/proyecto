<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\Cfdi;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Http\Responses\ApiResponse;
use nusoap_client;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
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
        $date = Carbon::now()->subDay(3)->toDateString();
        
        $hora = strtotime
        
        
        (Carbon::now()->addDay(-3)->toDateString()) == strtotime($request->fecha) ? Carbon::now()->addSecond(5)->toTimeString(): Carbon::now()->toTimeString();
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

        $rfcEmisor = $empresa[0]->rfc;
        $nombreEmisor = $empresa[0]->name;
        $cpEmisor =$empresa[0]->cp;
        $regimenFiscalEmisor = $empresa[0]->regime;
        $fechaVencimiento = $empresa[0]->vencimiento;


        if (strtotime(Carbon::now()->toDateString()) > strtotime($fechaVencimiento)) {
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

        $idCliente = 'd68ebd1b-949c-4b53-bd3b-34b9ac79b36d';
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
        $directorioSellos = storage_path().'/app/public/companies/'.$uuidCompany.'/CSD/';
        $directorio = storage_path().'/app/public/companies/'.$uuidCompany.'/comprobantes/'.$comprobante.'/';
        $xml = new Cfdi($version, $fecha, $noCertificado, $certificado, $subtotal, $moneda, $total, $tipoComprobante, $cpEmisor, $exportacion, $formaPago, $condicionesPago, $metodoPago, $descuento, $tipoCambio, $serie, $folio, $directorio);

        $xml->setEmisor($rfcEmisor, $nombreEmisor, $regimenFiscalEmisor);
        $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        
        if(count($relaciones) > 0){
            $xml->setUuidsRelacionados($relaciones);
        }
        $xml->setConceptos($conceptos);


        $xml->saveCfdi();

        $xsl = new \DOMDocument('1.0','UTF-8');
        // $xsl->loadXML(file_get_contents("https://www.sat.gob.mx/sitio_internet/cfd/4/cadenaoriginal_4_0/cadenaoriginal_4_0.xslt"));
        $xsl->loadXML(file_get_contents(app_path()."\Providers\Sat\cfdi40.xslt"));

        libxml_use_internal_errors(true);
        $proc = new \XSLTProcessor;
        $proc->importStyleSheet($xsl);

        $xml2 = new \DOMDocument;
        $xml2->load($directorio."generica.xml");

        $cadena = $proc->transformToXML($xml2);
        $cadena = $proc->transformToXML($xml2);
        $pkeyid = openssl_get_privatekey(file_get_contents($directorioSellos."CSD_Sucursal_1_EKU9003173C9_20230517_223850.key.pem"));
        openssl_sign($cadena, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
        openssl_free_key($pkeyid);
        $sello = base64_encode($signature);
        $xml->setSello($sello);
        $xml->saveCfdi();

        $metodo = "timbradoBase64Prueba";

        $data = array('contrato' => '97de57eb-d7f0-436e-af88-0b6d18783ad4', 'usuario' => 'adrian.rebollar@easysweb.com.mx', 'passwd' => 'C1nt3gr@n', 'cfdiXmlBase64' => base64_encode(file_get_contents($directorio."generica.xml")));
        $client = new nusoap_client('https://timbrado.pade.mx/servicio/Timbrado4.0?wsdl',true);
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = FALSE;

        $result = $client->call($metodo, $data);
        $respuesta = new \SimpleXMLElement($result['return']);
        $mensaje = (string) $respuesta->timbradoOk  == "false" ? (string) $respuesta->mensaje : "";


        if(!empty($mensaje)){
            return response()->json(['message' => $mensaje]);
        }

        // $xmlTemp = new SimpleXMLElement(base64_decode($respuesta->xmlBase64));
        // $arr = json_decode(json_encode((array)$xmlTemp), true);
        // $sello = $arr["@attributes"]['Sello'];
        // $nodo_comprobante->setAttribute("Sello", $sello);

        $uuid = (string)$respuesta->UUID;
        $fechaTimbrado = (string)$respuesta->FechaTimbrado;
        $selloCfdi = (string)$respuesta->selloCFD;
        $noCertificadoDoc = (string)$respuesta->noCertificadoSAT;
        $selloSat = (string)$respuesta->selloSAT;
        $rfcProveedor = "PPD101129EA3";

        $xml->setComplemento();
        $xml->setTimbreFiscal($uuid, $fechaTimbrado, $rfcProveedor, $selloCfdi, $noCertificadoDoc, $selloSat);
        $xml->saveCfdi();


        $factura = new Invoice;
        $factura->serie = '';
        $factura->folio = '';
        $factura->way_to_pay = $formaPago;
        $factura->payment_method = $metodoPago;
        $factura->payment_conditions = "hola";
        $factura->subtotal = $subtotal;
        $factura->discount = $descuento;
        $factura->currency = $moneda;
        $factura->change_type = intval($tipoCambio);
        $factura->payment_terms = '';
        $factura->total = $total;
        $factura->export = $exportacion;
        $factura->invoice_type =$tipoComprobante;
        // $factura->type_receipt = '';
        $factura->invoice_usage = $usoCfdi;
        $factura->uuid = $uuid;
        $factura->timbre_date = $fechaTimbrado;
        $factura->cfdi_seal = $selloCfdi;
        $factura->sat_seal = $selloSat;
        $factura->rfc_pac = $rfcProveedor;
        $factura->receiver_id = $idCliente;
        $factura->creation_date = '2024-04-13';
        // $factura->type_relation = '';
        $factura->uuid_company = $uuidCompany;
        $factura->save();

        return response()->json(['message' => "TODO CHIDO"]);
        
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
