<?php

namespace App\Http\Controllers;

//Modelos
use App\Models\Company;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\CompanyDetail;

//Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;


//Terceros
use App\Http\Requests\CompanyRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // var_dump(auth('api')->id());
        $user = auth('api')->user();

        if (!$user) {
            return ApiResponse::error('Usuario no autenticado o token incorrecto ', 401);
        }
        $tipo = $user->type;
        $userId = $user->id;
        switch ($tipo) {
            case "adm":
                $empresas = Company::select('companies.id', 'companies.name', 'companies.rfc', 'companies.status', 'company_details.tones_incluide')
                    ->join('company_details', 'company_details.company_id', '=', 'companies.id')
                    ->get();
                return ApiResponse::success('Datos obtenidos', 200, $empresas);

            case "col":
                $empresas = Company::select('companies.id', 'companies.name', 'companies.rfc', 'companies.status', 'company_details.tones_incluide')
                    ->join('collaborator_company', 'collaborator_company.company_id', '=', 'companies.id')
                    ->where('collaborator_company.collaborator_id', '=', $userId)
                    ->get();

                return ApiResponse::success('Datos obtenidos', 200, $empresas);

            default:
                $empresas = Company::select('companies.id', 'companies.name', 'companies.rfc', 'companies.status', 'company_details.tones_incluide')
                    ->join('company_details', 'company_details.company_id', '=', 'companies.id')
                    ->where('companies.id_usr_create', '=', $userId)
                    ->get();
                return ApiResponse::success('Datos obtenidos', 200, $empresas);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {

        try {
            DB::beginTransaction();
            // $idUsuario = 1;
            $idUsuario = auth('api')->id();
            $tipoEmpresa = (Company::where('id_usr_create', $idUsuario)->count() == 0) ? 'P' : 'H';
            $empresa = Company::create($request->validated());
            $idEmpresa = $empresa->id;

            // Crear directorios
            $directorioRaiz = 'companies/' . $idEmpresa;
            $directorios = [
                'comprobantes',  
                'logos',   
                'CSD'        
            ];

            // Crear los directorios principales
            foreach ($directorios as $directorio) {
                $rutaDirectorio = $directorioRaiz . '/' . $directorio;
                Storage::disk('public')->makeDirectory($rutaDirectorio);

                chmod(public_path('storage/' . $rutaDirectorio), 0777);
            }

            $comprobantes = ['ingreso', 'egreso', 'traslado' ,'pago', 'nomina']; 
            foreach ($comprobantes as $comprobante) {
                $rutaComprobante = $directorioRaiz . '/comprobantes/' . $comprobante;
                Storage::disk('public')->makeDirectory($rutaComprobante);

                chmod(public_path('storage/' . $rutaComprobante), 0777);
            }
    
            $timbresRegalo = 3;
            $fechaActual = date("Y-m-d");

            //TODO CHECAR ESTA LINEA PARA LOS HIJOS
            Company::where('id', $idEmpresa)->update(['type' => $tipoEmpresa, 'id_usr_create' => $idUsuario]);

            if ($tipoEmpresa == 'P') {
                $promociones = Promotion::where('status', 'Activo')->orderBy('id', 'DESC')->limit(1)->get();

                if ($promociones->count() > 0) {
                    $fechaFinal = $promociones[0]->date_end;
                    $timbresRegalo  = ($fechaActual > $fechaFinal) ? 0 : intval($promociones[0]->new_quantity);
                    if ($timbresRegalo > 0) {
                        $pagoEmpresa = new Payment;
                        $pagoEmpresa->type = 'R';
                        $pagoEmpresa->description = 'Timbres Regalos';
                        $pagoEmpresa->tones = 0;
                        $pagoEmpresa->gitf_tones = $timbresRegalo;
                        $pagoEmpresa->amount = 0;
                        $pagoEmpresa->date = $fechaActual;
                        $pagoEmpresa->company_id = $idEmpresa;
                        $pagoEmpresa->save();
                    }
                }
            }

            $detalleEmpresa = new CompanyDetail;
            $detalleEmpresa->tones_incluide = $timbresRegalo;
            $detalleEmpresa->pac_id = 3;
            $detalleEmpresa->fechaco = $fechaActual;
            $detalleEmpresa->fechaven = date("Y-m-d", strtotime($fechaActual . "+ 1 month"));
            $detalleEmpresa->company_id = $idEmpresa;
            $detalleEmpresa->save();

            DB::commit();
            $empresa = Company::select('companies.id', 'companies.name', 'companies.rfc', 'companies.status', 'company_details.tones_incluide')
                ->join('company_details', 'company_details.company_id', '=', 'companies.id')
                ->where('companies.id', '=', $idEmpresa)
                ->get();
            return ApiResponse::success('Empresa creada correctamente', 201, $empresa);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Errores de validación: ', 422, $errors);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error('Error al crear la empresa', 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $empresa = Company::findOrFail($id);
            return ApiResponse::success('Empresa obtenida exitosamente', 200, $empresa);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Empresa no encontrada', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, string $id)
    {
        try {

            $empresa = Company::findOrFail($id);
            $empresa->update($request->except('logo'));

            return ApiResponse::success('Empresa actualizada correctamente', 200, $empresa);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return ApiResponse::error('Errores de validacion: ', 422, $errors);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Empresa no encontrada', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
    
}
