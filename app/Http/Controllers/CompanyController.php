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

//Terceros
use App\Http\Requests\CompanyRequest;
use App\Http\Responses\ApiResponse;

class CompanyController extends Controller
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
    public function store(CompanyRequest $request)
    {

        try {
            DB::beginTransaction();
            $idUsuario = 1;

            $tipoEmpresa = (Company::where('id_usr_create', $idUsuario)->count() == 0) ? 'P': 'H';
            
            $empresa = Company::create($request->validated());
            $idEmpresa = $empresa->id;


            $timbresRegalo = 0;
            $fechaActual = date("Y-m-d");

            //TODO CHECAR ESTA LINEA PARA LOS HIJOS
            Company::where('id', $idEmpresa)->update(['type'=>$tipoEmpresa, 'id_usr_create' => $idUsuario]);

            if($tipoEmpresa == 'P'){
                $promociones = Promotion::where('status', 'Activo')->orderBy('id', 'DESC')->limit(1)->get();

                if($promociones->count() > 0){
                    $fechaFinal = $promociones[0]->date_end;
                    $timbresRegalo  = ($fechaActual > $fechaFinal) ? 0 : intval($promociones[0]->new_quantity);
                    if($timbresRegalo > 0){
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
            $detalleEmpresa->fechaven = date("Y-m-d", strtotime($fechaActual."+ 1 month"));
            $detalleEmpresa->company_id = $idEmpresa;
            $detalleEmpresa->save();

            DB::commit();
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
