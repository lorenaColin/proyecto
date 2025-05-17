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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\File; 
use NumberToWords\NumberToWords;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index2($uuid_company)
    {
        $invoices = Invoice::where('uuid_company', $uuid_company)->get();
        return response()->json([
            'message' => 'Facturas encontradas',
            'statusCode' => 200,
            'error' => false,
            'data' => $invoices
        ]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      
        try {

        // return response()->json(['message' => $request->uuid_company]);
        $serieFolio = explode('-', $request->serie_folio);
        $tipoComprobante = $request->invoice_type;
        // $folio = "";
        // $serie = $request->serie_folio;
        $serie = isset($serieFolio[0]) ? trim($serieFolio[0]) : null;
        $folio = isset($serieFolio[1]) ? trim($serieFolio[1]) : null;
        $date = Carbon::now()->subDay(3)->toDateString();
        
        $hora = strtotime
        
        
        (Carbon::now()->addDay(-3)->toDateString()) == strtotime($request->fecha) ? Carbon::now()->addSecond(5)->toTimeString(): Carbon::now()->toTimeString();
        $fecha = $request->fecha."T".$hora;

        $usoCfdi = $request->uso_cfdi;
        // $metodoPago = $request->metodo_pago;
        // $formaPago = $request->forma_pago;
        $formaPago  = $request->forma_pago ?? null;
        $metodoPago = $request->metodo_pago ?? null;

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
            if ($empresa->isEmpty()) {
                return ApiResponse::error('Error', 404, 'Empresa no encontrada');
            }
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

        // $idCliente = 'd68ebd1b-949c-4b53-bd3b-34b9ac79b36d';
        $idCliente = $request->receptor;
        $cliente = Customer::select('name', 'rfc', 'cp', 'regime', 'residence', 'num_reg_id_trib')->where('id', '=', $idCliente)->where('company_id', '=', $uuidCompany)->get();
        if ($cliente->isEmpty()) {
            return ApiResponse::error('Error', 404, 'Cliente no encontrado');
        }
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
                $conceptos  = json_decode('[{"cantidad":"1.000000","descripcion":"Pago","claveProdServ":"84111506","claveUnidad":"ACT","unidad":"","valorUnitario":"0.000000","objetoImp":"01","importe":"0.000000","descuento":"0.000000","noIdentificacion":""}]', true);
                $comprobante = 'pago';
                break;
            case 'N':
                $conceptos  = json_decode('[{"cantidad":"1.000000","descripcion":"Pago de nómina","claveProdServ":"84111505","claveUnidad":"ACT","valorUnitario":"0.000000","descuento":"0.00","objetoImp":"01","importe":"0.000000"}]', true);
                $comprobante = 'nomina';
                break;
            case 'E':
                $comprobante = 'egreso';
                $conceptos  = $concepts;
                break;
            case 'T':
                $comprobante = 'traslado';
                $conceptos  = $concepts;
                break;
            default:
                $comprobante = 'ingreso';
                $conceptos  = $concepts;
        }
        $directorioSellos = storage_path().'/app/public/companies/'.$uuidCompany.'/CSD/';
        $directorio = storage_path().'/app/public/companies/'.$uuidCompany.'/comprobantes/'.$comprobante.'/';
        if (! is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        $keyPath = $directorioSellos . 'CSD_Sucursal_1_EKU9003173C9_20230517_223850.key.pem';
if (file_exists($keyPath)) {
    try {
        $pkeyContent = @file_get_contents($keyPath);
        if ($pkeyContent !== false) {
            $pkeyid = openssl_get_privatekey($pkeyContent);
            openssl_sign($cadena, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
            openssl_free_key($pkeyid);

            $sello = base64_encode($signature);
            $xml->setSello($sello);
            $xml->saveCfdi(); 
        } else {
            Log::warning("No se pudo leer la llave en $keyPath");
        }
    } catch (\Throwable $e) {
        Log::warning("Error firmando con llave ($keyPath): ".$e->getMessage());
    }
} else {
    Log::warning("Archivo llave no encontrado, se omite firma: $keyPath");
}
        
        $xml = new Cfdi($version, $fecha, $noCertificado, $certificado, $subtotal, $moneda, $total, $tipoComprobante, $cpEmisor, $exportacion, $formaPago, $condicionesPago, $metodoPago, $descuento, $tipoCambio, $serie, $folio, $directorio);

        $xml->setEmisor($rfcEmisor, $nombreEmisor, $regimenFiscalEmisor);
        $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        
        if(count($relaciones) > 0){
            $xml->setUuidsRelacionados($relaciones);
        }
        $xml->setConceptos($conceptos);
        if ($request->filled('complemento_carta_porte') && !empty($request->input('complemento_carta_porte'))) {
            $cp = $request->input('complemento_carta_porte');
            $xml->agregarComplementoCartaPorte($cp);
        }
        
        $timestamp = date('Ymd_His');                    // p.ej. 20250423_154501
        $uniqueName = "cfdi_{$serie}_{$folio}_{$timestamp}.xml";
        $xml->saveCfdi($uniqueName);

        $dummyUuid          = \Illuminate\Support\Str::uuid()->toString();
        $dummyFechaTimbrado = now()->format('Y-m-d\TH:i:s');
        $dummyRfcProv       = 'AAA010101AAA';
        $dummySelloCFD      = 'SelloDelComprobanteSimulado';
        $dummyNoCertSAT     = '00001000000555555555';
        $dummySelloSAT      = 'SelloDelSATSimulado';
    
        // 2) Asegúrate de que exista el nodo <cfdi:Complemento>
        $xml->setComplemento();
    
        // 3) Inyecta el timbre fiscal digital simulado
        $xml->setTimbreFiscal(
            $dummyUuid,
            $dummyFechaTimbrado,
            $dummyRfcProv,
            $dummySelloCFD,
            $dummyNoCertSAT,
            $dummySelloSAT
        );
    
        // 4) Vuelve a grabar el XML ya con el complemento dentro
        $xml->saveCfdi($uniqueName);
        // ————————————————
// 1) Parsear el XML recién guardado para obtener los conceptos
// ————————————————

$xmlPath = storage_path("app/public/companies/{$uuidCompany}/comprobantes/{$comprobante}/{$uniqueName}");

$xmlContent = simplexml_load_file($xmlPath);
$namespaces = $xmlContent->getNamespaces(true);
$xmlContent->registerXPathNamespace('cfdi', $namespaces['cfdi']);
$conceptosXml = $xmlContent->xpath('//cfdi:Concepto');

$conceptosArray = [];
foreach ($conceptosXml as $nodo) {
    $attrs = $nodo->attributes();
    $conceptosArray[] = [
        'descripcion'      => (string) $attrs['Descripcion'],
        'cantidad'         => (float)  $attrs['Cantidad'],
        'unidad'           => (string) $attrs['Unidad'],
        'valor_unitario'   => (float)  $attrs['ValorUnitario'],
        'importe'          => (float)  $attrs['Importe'],
    ];
}

        $cadena = null;
        $xml->saveCfdi('generica.xml');
        if (class_exists('XSLTProcessor')) {
        $xsl = new \DOMDocument('1.0','UTF-8');
        $xsl->loadXML(file_get_contents(app_path()."\Providers\Sat\cfdi40.xslt"));
        if (!$xsl) {
            die("Error: No se pudo parsear el XSL correctamente.");
        }
        libxml_use_internal_errors(true);
        $proc = new \XSLTProcessor;
        $proc->importStyleSheet($xsl);
        if (!$proc) {
            die("Error al crear el objeto XSLTProcessor.");
        }
        if (!$proc->importStyleSheet($xsl)) {
            die("Error al importar la hoja de estilo XSL.");
        }
        $xml2 = new \DOMDocument;
        if (!$xml2->load($directorio."generica.xml")) {
            die("Error: No se pudo cargar el archivo XML en: $directorio generica.xml");
        }
        $cadena = $proc->transformToXML($xml2);
        $cadena = $proc->transformToXML($xml2);
        if ($cadena === false || empty($cadena)) {
            die("Error: La transformación XSLT falló o no produjo resultados.");
        }
         } else {
        echo "La clase XSLTProcessor no está disponible.";
    }
     

        $metodo = "timbradoBase64Prueba";

        $data = array('contrato' => '97de57eb-d7f0-436e-af88-0b6d18783ad4', 'usuario' => 'adrian.rebollar@easysweb.com.mx', 'passwd' => 'C1nt3gr@n', 'cfdiXmlBase64' => base64_encode(file_get_contents($directorio."generica.xml")));
        $client = new nusoap_client('https://timbrado.pade.mx/servicio/Timbrado4.0?wsdl',true);
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = FALSE;

        $result = $client->call($metodo, $data);
        $respuesta = new \SimpleXMLElement($result['return']);
        $mensaje = (string) $respuesta->timbradoOk  == "false" ? (string) $respuesta->mensaje : "";


        if(!empty($mensaje)){
            // Aquí puedes loguear el error o simplemente continuar sin detener el proceso
            Log::error("Error de timbrado: " . $mensaje);
        }

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
        $factura->serie = $serie;
        $factura->folio = $folio;
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
        $factura->date = Carbon::now();
        $factura->xml_filename = $uniqueName;
          $factura->pdf_filename  = null;

 
    $factura->save();
$numberToWords = new NumberToWords();
$transformer   = $numberToWords->getNumberTransformer('es');
$factura->monto_letra = strtoupper($transformer->toWords($factura->total)) . ' PESOS 00/100 M.N.';

// 4) Generar el PDF enviando factura + conceptos
$pdf = Pdf::loadView('facturas.pdf', [
    'factura'   => $factura,
    'conceptos' => $conceptosArray,
]);
$pdfDir = storage_path("app/public/facturas/{$uuidCompany}/pdf/");
if (!\File::exists($pdfDir)) {
    \File::makeDirectory($pdfDir, 0755, true);
}

// 6) Definir nombre y guardar
$pdfName = "factura_{$serie}_{$folio}_{$uuid}.pdf";
$pdf->save($pdfDir . $pdfName);

// 7) Actualizar registro
$factura->pdf_filename = $pdfName;
$factura->save();
    // Responder con éxito
    return response()->json(['message' => "Factura guardada y PDF generado correctamente"]);

} catch (\Throwable $e) {
    Log::error('Error en store Invoice: ' . $e->getMessage(), [
        'line' => $e->getLine(),
        'file' => $e->getFile(),
    ]);
    return response()->json([
        'error'  => 'Error interno al procesar CFDI',
        'detail' => $e->getMessage(),
    ], 500);
}
    }
    // public function store(Request $request)
    // {
    //     // return response()->json(['message' => $request->uuid_company]);
    //     $tipoComprobante = $request->invoice_type;
    //     $folio = "";
    //     $serie = $request->serie_folio;
    //     $date = Carbon::now()->subDay(3)->toDateString();
        
    //     $hora = strtotime
        
        
    //     (Carbon::now()->addDay(-3)->toDateString()) == strtotime($request->fecha) ? Carbon::now()->addSecond(5)->toTimeString(): Carbon::now()->toTimeString();
    //     $fecha = $request->fecha."T".$hora;

    //     $usoCfdi = $request->uso_cfdi;
    //     $metodoPago = $request->metodo_pago;
    //     $formaPago = $request->forma_pago;
    //     $moneda = $request->moneda;
    //     $tipoCambio = $request->tipoCambio;
        
    //     $subtotal = $request->subtotal;
    //     $total = $request->total;
    //     $descuento = $request->descuento;
    //     $retenciones = $request->retenciones;

    //     $condicionesPago = $request->condiciones;


    //     $traslados = $request->traslados;
    //     $relaciones = $request->relaciones;
    //     $concepts = $request->concepts;

    //     $uuidCompany = $request->uuid_company;

        

    //     $empresa = Company::select('companies.name', 'companies.rfc', 'companies.cp', 'companies.regime', 'company_details.fechaven as vencimiento', 'company_details.tones_incluide as timbres', 'company_details.expiration_date_cert as vencimientoCertificado', 'company_details.certificate as noCertificado', 'company_details.contcert as certificado' )
    //         ->join('company_details', 'company_details.company_id', '=', 'companies.id')
    //         ->where('companies.id', '=', $uuidCompany)
    //         ->get();
    //         if ($empresa->isEmpty()) {
    //             return ApiResponse::error('Error', 404, 'Empresa no encontrada');
    //         }
    //     $rfcEmisor = $empresa[0]->rfc;
    //     $nombreEmisor = $empresa[0]->name;
    //     $cpEmisor =$empresa[0]->cp;
    //     $regimenFiscalEmisor = $empresa[0]->regime;
    //     $fechaVencimiento = $empresa[0]->vencimiento;


    //     if (strtotime(Carbon::now()->toDateString()) > strtotime($fechaVencimiento)) {
    //         return ApiResponse::error('Error', 404, 'Cuenta vencida');
    //     }
        
    //     $timbres = $empresa[0]->timbres;
    //     if(intval($timbres) == 0){
    //         return ApiResponse::error('Error', 404, 'Sin timbres');
    //     }
        
    //     $noCertificado = $empresa[0]->noCertificado;
    //     $certificado = $empresa[0]->certificado;

    //     // if(!isset($noCertificado)){
    //     //     return ApiResponse::error('Error', 404, 'No haz subido tus sellos CSD');
    //     // }

    //     // $vencimientoCertificado = substr($empresa[0]->vencimientoCertificado, 0, 19);
    //     // return ApiResponse::error('Error', 404, $vencimientoCertificado);

    //     $idCliente = 'd68ebd1b-949c-4b53-bd3b-34b9ac79b36d';
    //     $cliente = Customer::select('name', 'rfc', 'cp', 'regime', 'residence', 'num_reg_id_trib')->where('id', '=', $idCliente)->where('company_id', '=', $uuidCompany)->get();
    //     if ($cliente->isEmpty()) {
    //         return ApiResponse::error('Error', 404, 'Cliente no encontrado');
    //     }
    //     $nombreReceptor = $cliente[0]->name;
    //     $rfcReceptor = $cliente[0]->rfc;
    //     $domicilioReceptor = $cliente[0]->cp;
    //     $regimenFiscalReceptor = $cliente[0]->regime;
    //     $residenciaFiscal = $cliente[0]->residence;
    //     $numRegIdTrib = $cliente[0]->num_reg_id_trib;

    //     $exportacion = "01";
        
    //     $version = "4.0";

    //     switch ($tipoComprobante) {
    //         case 'P':
    //             $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago", "claveProdServ":"84111506", "claveUnidad":"ACT", "unidad":"", "valorUnitario":"0.000000", "objetoImp":"01", "importe":"0.000000", "descuento": "0.000000", "noIdentificacion": ""}]', true);
    //             break;
    //         case 'N':
    //             $listaConceptos = json_decode('[{"cantidad":"1.000000", "descripcion":"Pago de nómina", "claveProdServ":"84111505", "claveUnidad":"ACT", "valorUnitario": "0.000000", "descuento":"0.00", "objetoImp":"01", "importe":"0.000000"}]', true);
    //             break;
    //         default:
    //             $conceptos = $concepts;
    //             switch ($tipoComprobante) {
    //                 case 'E':
    //                     $comprobante = 'egreso';
    //                     break;
    //                 case 'T':
    //                     $comprobante = 'traslado';
    //                     break;
    //                 default:
    //                     $comprobante = 'ingreso';
    //                     break;
    //             }
    //             break;
    //     }
    //     // return response()->json(['message' => count($relaciones)]);
    //     $directorioSellos = storage_path().'/app/public/companies/'.$uuidCompany.'/CSD/';
    //     $directorio = storage_path().'/app/public/companies/'.$uuidCompany.'/comprobantes/'.$comprobante.'/';
    //     $xml = new Cfdi($version, $fecha, $noCertificado, $certificado, $subtotal, $moneda, $total, $tipoComprobante, $cpEmisor, $exportacion, $formaPago, $condicionesPago, $metodoPago, $descuento, $tipoCambio, $serie, $folio, $directorio);

    //     $xml->setEmisor($rfcEmisor, $nombreEmisor, $regimenFiscalEmisor);
    //     $xml->setReceptor($rfcReceptor, $nombreReceptor, $domicilioReceptor, $regimenFiscalReceptor, $usoCfdi, $residenciaFiscal, $numRegIdTrib);
        
    //     if(count($relaciones) > 0){
    //         $xml->setUuidsRelacionados($relaciones);
    //     }
    //     $xml->setConceptos($conceptos);


    //     $xml->saveCfdi();
    //     $cadena = null;
    //     if (class_exists('XSLTProcessor')) {
    //     $xsl = new \DOMDocument('1.0','UTF-8');
    //     // $xsl->loadXML(file_get_contents("https://www.sat.gob.mx/sitio_internet/cfd/4/cadenaoriginal_4_0/cadenaoriginal_4_0.xslt"));
    //     $xsl->loadXML(file_get_contents(app_path()."\Providers\Sat\cfdi40.xslt"));
    //     if (!$xsl) {
    //         die("Error: No se pudo parsear el XSL correctamente.");
    //     }
    //     libxml_use_internal_errors(true);
    //     $proc = new \XSLTProcessor;
    //     $proc->importStyleSheet($xsl);
    //     if (!$proc) {
    //         die("Error al crear el objeto XSLTProcessor.");
    //     }
    //     if (!$proc->importStyleSheet($xsl)) {
    //         die("Error al importar la hoja de estilo XSL.");
    //     }
    //     $xml2 = new \DOMDocument;
    //     if (!$xml2->load($directorio."generica.xml")) {
    //         die("Error: No se pudo cargar el archivo XML en: $directorio generica.xml");
    //     }
    //     $cadena = $proc->transformToXML($xml2);
    //     $cadena = $proc->transformToXML($xml2);
    //     if ($cadena === false || empty($cadena)) {
    //         die("Error: La transformación XSLT falló o no produjo resultados.");
    //     }
    //      } else {
    //     // Maneja el error si la clase no está disponible
    //     echo "La clase XSLTProcessor no está disponible.";
    // }
    //     $pkeyid = openssl_get_privatekey(file_get_contents($directorioSellos."CSD_Sucursal_1_EKU9003173C9_20230517_223850.key.pem"));
    //     openssl_sign($cadena, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
    //     openssl_free_key($pkeyid);
    //     $sello = base64_encode($signature);
    //     $xml->setSello($sello);
    //     $xml->saveCfdi();

    //     $metodo = "timbradoBase64Prueba";

    //     $data = array('contrato' => '97de57eb-d7f0-436e-af88-0b6d18783ad4', 'usuario' => 'adrian.rebollar@easysweb.com.mx', 'passwd' => 'C1nt3gr@n', 'cfdiXmlBase64' => base64_encode(file_get_contents($directorio."generica.xml")));
    //     $client = new nusoap_client('https://timbrado.pade.mx/servicio/Timbrado4.0?wsdl',true);
    //     $client->soap_defencoding = 'UTF-8';
    //     $client->decode_utf8 = FALSE;

    //     $result = $client->call($metodo, $data);
    //     $respuesta = new \SimpleXMLElement($result['return']);
    //     $mensaje = (string) $respuesta->timbradoOk  == "false" ? (string) $respuesta->mensaje : "";


    //     if(!empty($mensaje)){
    //         return response()->json(['message' => $mensaje]);
    //     }

    //     // $xmlTemp = new SimpleXMLElement(base64_decode($respuesta->xmlBase64));
    //     // $arr = json_decode(json_encode((array)$xmlTemp), true);
    //     // $sello = $arr["@attributes"]['Sello'];
    //     // $nodo_comprobante->setAttribute("Sello", $sello);

    //     $uuid = (string)$respuesta->UUID;
    //     $fechaTimbrado = (string)$respuesta->FechaTimbrado;
    //     $selloCfdi = (string)$respuesta->selloCFD;
    //     $noCertificadoDoc = (string)$respuesta->noCertificadoSAT;
    //     $selloSat = (string)$respuesta->selloSAT;
    //     $rfcProveedor = "PPD101129EA3";

    //     $xml->setComplemento();
    //     $xml->setTimbreFiscal($uuid, $fechaTimbrado, $rfcProveedor, $selloCfdi, $noCertificadoDoc, $selloSat);
    //     $xml->saveCfdi();


    //     $factura = new Invoice;
    //     $factura->serie = '';
    //     $factura->folio = '';
    //     $factura->way_to_pay = $formaPago;
    //     $factura->payment_method = $metodoPago;
    //     $factura->payment_conditions = "hola";
    //     $factura->subtotal = $subtotal;
    //     $factura->discount = $descuento;
    //     $factura->currency = $moneda;
    //     $factura->change_type = intval($tipoCambio);
    //     $factura->payment_terms = '';
    //     $factura->total = $total;
    //     $factura->export = $exportacion;
    //     $factura->invoice_type =$tipoComprobante;
    //     // $factura->type_receipt = '';
    //     $factura->invoice_usage = $usoCfdi;
    //     $factura->uuid = $uuid;
    //     $factura->timbre_date = $fechaTimbrado;
    //     $factura->cfdi_seal = $selloCfdi;
    //     $factura->sat_seal = $selloSat;
    //     $factura->rfc_pac = $rfcProveedor;
    //     $factura->receiver_id = $idCliente;
    //     $factura->creation_date = '2024-04-13';
    //     // $factura->type_relation = '';
    //     $factura->uuid_company = $uuidCompany;
    //     $factura->save();

    //     return response()->json(['message' => "TODO CHIDO"]);
        
    // }
    public function downloadXml(Invoice $invoice)
    {
        // Determinar la carpeta según el tipo de factura
        $folder = match ($invoice->invoice_type) {
            'I' => 'ingreso',
            'E' => 'egreso',
            'T' => 'traslado',
            default => 'otros', // Puedes eliminar esta línea si solo manejas I, E y T
        };
    
        // Construir la ruta al archivo
        $path = public_path(
            'storage/companies/'
            . $invoice->uuid_company
            . '/comprobantes/'
            . $folder
            . '/'
            . $invoice->xml_filename
        );
    
        // Verificar si el archivo existe
        if (!file_exists($path)) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }
    
        // Descargar el archivo
        return response()->download($path, $invoice->xml_filename, [
            'Content-Type' => 'application/xml'
        ]);
    }
public function descargarPDFPorId($id)
{
    $factura = Invoice::findOrFail($id);
    $path    = storage_path("app/public/facturas/{$factura->uuid_company}/pdf/{$factura->pdf_filename}");
    return response()->download($path, $factura->pdf_filename);
}

    public function obtenerUUIDs()
    {
        $ruta = storage_path('app/public/companies/30198446-cddb-4696-91b5-55734256d46d/comprobantes/ingreso');
        $files = File::allFiles($ruta);
        $uuids = [];
    
        foreach ($files as $file) {
            $xmlContent = File::get($file);
    
            // Quitar namespace de cartaporte31 si no se necesita
            $xmlContent = str_replace('xmlns:cartaporte31="http://www.sat.gob.mx/CartaPorte31"', '', $xmlContent);
    
            libxml_use_internal_errors(true); // Oculta errores de namespaces
            $xml = simplexml_load_string($xmlContent);
            if ($xml === false) {
                continue; // Saltar archivos con errores
            }
    
            $namespaces = $xml->getNamespaces(true);
    
            if (isset($namespaces['cfdi'])) {
                $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
            }
    
            if (isset($namespaces['tfd'])) {
                $xml->registerXPathNamespace('tfd', $namespaces['tfd']);
                $uuid = $xml->xpath('//cfdi:Complemento//tfd:TimbreFiscalDigital/@UUID');
    
                if (isset($uuid[0])) {
                    $uuids[] = (string)$uuid[0];
                }
            }
        }
    
        return response()->json([
            'message' => 'UUIDs procesados con éxito',
            'uuids' => $uuids
        ]);
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
