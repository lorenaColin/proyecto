<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDetailRequest;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyDetailRequest $request) {}

    public function createOrUpdateSeals(CompanyDetailRequest $request)
    {
        try {
            $companyId = $request->input('company_id');
            $fileCert = $request->file('certificate');
            $fileKey = $request->file('private_key');
            $password = $request->input('password_key');

            $disk = 'public'; 

            $nombreArchivoCertificado = $fileCert->getClientOriginalName();
            $nombreArchivoLlave = $fileKey->getClientOriginalName();
    
            $directorio = 'customers/'.$companyId.'/certificates/';
            $directorioCompleto = 'app/public/'.$directorio;
    
            // Almacenamiento de los archivos
            $fileCert->storeAs($directorio, $nombreArchivoCertificado, $disk);
            $fileKey->storeAs($directorio, $nombreArchivoLlave, $disk);
    
            $ubicacionCertificado = storage_path($directorioCompleto.$nombreArchivoCertificado);
            $ubicacionLlave = storage_path($directorioCompleto.$nombreArchivoLlave);
    
            // Verificar si el certificado es válido (CSD)
            $isValidCert = shell_exec('openssl x509 -inform DER -in '.$ubicacionCertificado.' -subject -noout');
            if (count(explode('OU=', $isValidCert)) == 1 && count(explode('OU = ', $isValidCert)) == 1) {
                unlink($ubicacionCertificado);
                unlink($ubicacionLlave);
                return response()->json(['error' => true, 'msg' => 'El certificado no es un Certificado de Sello Digital (CSD)']);
            }
    
            // Desencriptar la llave privada
            $generarLlave = shell_exec('openssl pkcs8 -inform DER -in '.$ubicacionLlave.' -out '.Storage::disk($disk)->path($directorio.$nombreArchivoLlave.'.pem').' -passin pass:'.$password.' 2>&1');
            if (strpos($generarLlave, 'Error decrypting') !== false) {
                unlink(storage_path($directorioCompleto.$nombreArchivoLlave.'.pem'));
                unlink($ubicacionCertificado);
                unlink($ubicacionLlave);
                return response()->json(['error' => true, 'msg' => 'La contraseña es incorrecta.']);
            }
    
            // Verificar que las llaves coincidan
            $moduloCertificado = shell_exec('openssl x509 -noout -modulus -in '.$ubicacionCertificado.' 2>&1');
            $moduloLlave = shell_exec('openssl rsa -noout -modulus -in '.$ubicacionLlave.'.pem  2>&1');
            if ($moduloCertificado != $moduloLlave) {
                unlink(storage_path($directorioCompleto.$nombreArchivoLlave.'.pem'));
                unlink($ubicacionCertificado);
                unlink($ubicacionLlave);
                return response()->json(['error' => true, 'msg' => 'El par de llaves no coinciden.']);
            }
    
            // Obtener fechas de validez del certificado
            $fechaInicial = $this->convertDate(str_replace('notBefore=', '', shell_exec('openssl x509 -noout -in '.$ubicacionCertificado.' -startdate')));
            $fechaVigencia = $this->convertDate(str_replace('notAfter=', '', shell_exec('openssl x509 -noout -in '.$ubicacionCertificado.' -enddate')));
            $fechaActual = strtotime(date('d-m-Y H:i:00', time()));
    
            // Verificar si el certificado ha expirado
            if (strtotime(substr($fechaVigencia, 0, 19)) < $fechaActual) {
                unlink(storage_path($directorioCompleto.$nombreArchivoLlave.'.pem'));
                unlink($ubicacionCertificado);
                unlink($ubicacionLlave);
                return response()->json(['error' => true, 'msg' => 'El certificado ha expirado: '.strftime('%d de %B de %Y.', strtotime(substr($fechaVigencia, 0, 19)))]);
            }
    
            // Convertir el certificado a formato PEM
            $certificadoPem = shell_exec('openssl x509 -in '.$ubicacionCertificado.' -inform DER -out  '.Storage::disk($disk)->path($directorio.$nombreArchivoCertificado.'.pem'));
    
            // Crear archivo de cancelación
            $archivoCancelacion = shell_exec('openssl pkcs12 -export -out '.Storage::disk($disk)->path($directorio.'cancelacion.pfx').' -inkey '.$ubicacionLlave.'.pem -in '.$ubicacionCertificado.'.pem -passout pass:'.$password);
    
            // Obtener el serial del certificado
            $noSerial = pack('H*', trim(str_replace('serial=', '', shell_exec('openssl x509 -noout -in '.$ubicacionCertificado.' -serial'))));
    
            // Obtener el contenido del certificado
            $contenidoCertificado = file($ubicacionCertificado.'.pem');
            unset($contenidoCertificado[count($contenidoCertificado) - 1]);
            unset($contenidoCertificado[0]);
    
            $contenidoCertificado = trim(implode('', $contenidoCertificado));
            $contenidoCertificado = str_replace("\r", "", str_replace("\n", "", $contenidoCertificado));
    
            // Renombrar el archivo del certificado y su PEM
            rename($ubicacionCertificado, storage_path($directorioCompleto.$noSerial.'.cer'));
            rename($ubicacionCertificado.'.pem', storage_path($directorioCompleto.$noSerial.'.cer.pem'));
    

            // Actualizar o crear el registro de la compañía
            $seal = CompanyDetail::updateOrCreate(
                ['company_id' => $companyId],
                [
                    // 'tones_incluide' => $request->input('tones_incluide'),
                    // 'pac_id' => $request->input('pac_id'),
                    'certificate' => $noSerial,
                    'private_key' => $nombreArchivoLlave,
                    'password_key' => $password,
                    'contcert' => $contenidoCertificado,
                    'expiration_date_cert' => $fechaVigencia,
                    'start_date_cert' => $fechaInicial,
                    // 'fechaco' => $request->input('fechaco'),
                    // 'fechaven' => $request->input('fechaven'),
                    // 'sta_prod' => $request->input('sta_prod'),
                ]
            );

            return response()->json(['error' => false, 'msg' => 'Sellos actualizados correctamente.', 'seal_id' => $seal->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'msg' => 'Ocurrió un error: ' . $e->getMessage()]);
        }
    }

    public function convertDate($date) {
        $info_preg = array();
        $date = str_replace('  ', ' ', $date);
        preg_match('#([A-z]{3}) ([0-9]{1,2}) ([0-2][0-9]:[0-5][0-9]:[0-5][0-9]) ([0-9]{4})#', $date, $info_preg);
        $fecha =  $info_preg[2].'-'.$info_preg[1].'-'.$info_preg[4].' '.$info_preg[3];
        return date('d-m-Y H:i:s a', strtotime($fecha));
    }
    /**
     * Display the specified resource.
     */
    public function show(CompanyDetail $companyDetail)
    {
        //
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
