<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDetailRequest;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Responses\ApiResponse;


class CompanyDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $figuras = CompanyDetail::all();
        return ApiResponse::success('Listado de figuras', 200, $figuras); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyDetailRequest $request) {}

    public function loadSeals(CompanyDetailRequest $request)
    {
        try {
            $companyId = $request->input('company_id');
            $fileCert = $request->file('certificate');
            $fileKey = $request->file('private_key');
            $password = $request->input('password_key');

            $disk = 'public';
            $tempDir = "companies/$companyId/CSD/temp/";
            $prodDir = "companies/$companyId/CSD/";

            if (!Storage::disk($disk)->exists($tempDir)) {
                Storage::disk($disk)->makeDirectory($tempDir);
            }

            $nombreArchivoCertificado = $fileCert->getClientOriginalName();
            $nombreArchivoLlave = $fileKey->getClientOriginalName();

            $fileCert->storeAs($tempDir, $nombreArchivoCertificado, $disk);
            $fileKey->storeAs($tempDir, $nombreArchivoLlave, $disk);

            $ubicacionCertificadoTemp = storage_path("app/$disk/$tempDir$nombreArchivoCertificado");
            $ubicacionLlaveTemp = storage_path("app/$disk/$tempDir$nombreArchivoLlave");

            // Validar el certificado
            $isValidCert = shell_exec('openssl x509 -inform DER -in ' . $ubicacionCertificadoTemp . ' -subject -noout');
            if (count(explode('OU=', $isValidCert)) == 1 && count(explode('OU = ', $isValidCert)) == 1) {
                Storage::disk($disk)->deleteDirectory($tempDir);
                return response()->json(['error' => true, 'msg' => 'El certificado no es un Certificado de Sello Digital (CSD)']);
            }

            $generarLlave = shell_exec('openssl pkcs8 -inform DER -in ' . $ubicacionLlaveTemp . ' -out ' . storage_path("app/$disk/$tempDir$nombreArchivoLlave.pem") . ' -passin pass:' . $password . ' 2>&1');
            if (strpos($generarLlave, 'Error decrypting') !== false) {
                Storage::disk($disk)->deleteDirectory($tempDir);
                return response()->json(['error' => true, 'msg' => 'La contraseña es incorrecta.']);
            }

            $moduloCertificado = shell_exec('openssl x509 -noout -modulus -in ' . $ubicacionCertificadoTemp . ' 2>&1');
            $moduloLlave = shell_exec('openssl rsa -noout -modulus -in ' . $ubicacionLlaveTemp . '.pem  2>&1');
            if ($moduloCertificado != $moduloLlave) {
                Storage::disk($disk)->deleteDirectory($tempDir);
                return response()->json(['error' => true, 'msg' => 'El par de llaves no coinciden.']);
            }

            // Verificar vigencia del certificado
            $fechaVigencia = shell_exec('openssl x509 -noout -enddate -in ' . $ubicacionCertificadoTemp);
            $fechaVigencia = str_replace('notAfter=', '', $fechaVigencia);
            $fechaActual = time();

            if (strtotime(substr($fechaVigencia, 0, 19)) < $fechaActual) {
                unlink(storage_path("$disk/$tempDir$nombreArchivoLlave.pem"));
                unlink($ubicacionCertificadoTemp);
                unlink($ubicacionLlaveTemp);

                return response()->json(['error' => true, 'msg' => 'El certificado ha expirado: ' . strftime('%d de %B de %Y', strtotime($fechaVigencia))]);
            }

            // Generar certificado PEM
            $certificadoPem = shell_exec('openssl x509 -in ' . $ubicacionCertificadoTemp . ' -inform DER -out ' . Storage::disk($disk)->path("$tempDir$nombreArchivoCertificado.pem"));

            // Generar archivo de cancelación
            $archivoCancelacion = shell_exec('openssl pkcs12 -export -out ' . Storage::disk($disk)->path("$tempDir/cancelacion.pfx") . ' -inkey ' . $ubicacionLlaveTemp . '.pem -in ' . $ubicacionCertificadoTemp . '.pem -passout pass:' . $password);

            // Obtener el número de serie del certificado
            $noSerial = pack('H*', trim(str_replace('serial=', '', shell_exec('openssl x509 -noout -in ' . $ubicacionCertificadoTemp . ' -serial'))));

            // Procesar contenido del certificado
            $contenidoCertificado = file($ubicacionCertificadoTemp . '.pem');
            unset($contenidoCertificado[count($contenidoCertificado) - 1], $contenidoCertificado[0]);
            $contenidoCertificado = trim(implode('', $contenidoCertificado));
            $contenidoCertificado = str_replace(["\r", "\n"], '', $contenidoCertificado);

            // Renombrar archivos
            rename($ubicacionCertificadoTemp, storage_path("$disk/$prodDir$noSerial.cer"));
            rename($ubicacionCertificadoTemp . '.pem', storage_path("$disk/$prodDir$noSerial.cer.pem"));

            // Limpiar carpeta temporal
            Storage::disk($disk)->deleteDirectory($tempDir);

            // Actualizar informacion en CompanyDetails
            $seals = CompanyDetail::where('company_id', $companyId)->first();
            if ($seals) {
                $seals->update([
                    'certificate' => $noSerial,
                    'private_key' => $nombreArchivoLlave,
                    'password_key' => $password,
                    'contcert' => $contenidoCertificado,
                    'expiration_date_cert' => $fechaVigencia,
                    'start_date_cert' => $fechaActual,
                ]);
            }
            $idDB = $seals->id;

            return response()->json(['error' => false, 'msg' => 'Sellos(CSD) actualizados correctamente.', 'fechaInicial' => $fechaActual, 'fechaVigencia' => $fechaVigencia, 'idDB' => $idDB]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'msg' => 'Ocurrió un error: ' . $e->getMessage()]);
        }
    }

    public function convertDate($date)
    {
        $info_preg = array();
        $date = str_replace('  ', ' ', $date);
        preg_match('#([A-z]{3}) ([0-9]{1,2}) ([0-2][0-9]:[0-5][0-9]:[0-5][0-9]) ([0-9]{4})#', $date, $info_preg);
        $fecha =  $info_preg[2] . '-' . $info_preg[1] . '-' . $info_preg[4] . ' ' . $info_preg[3];
        return date('d-m-Y H:i:s a', strtotime($fecha));
    }
    /**
     * Display the specified resource.
     */
    public function show($company_id)
    {
        // Buscar el detalle de la compañía usando company_id
        $figuras = CompanyDetail::where('company_id', $company_id)->first();
        
        // Verificar si se encuentra el registro
        if (!$figuras) {
            return ApiResponse::error('Company no encontrada', 404, 'La company con el company_id proporcionado no existe');
        }
    
        return ApiResponse::success('Detalle de la company', 200, $figuras);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyDetail $companyDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyDetail $companyDetail)
    {
        //
    }
}
