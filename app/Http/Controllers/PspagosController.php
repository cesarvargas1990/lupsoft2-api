<?php

namespace App\Http\Controllers;

use App\Pspagos;
use App\Psprestamos;
use App\Psfechaspago;
use Carbon\Carbon;
use Illuminate\Http\Request;

use DB;

class PspagosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Generic for tables, make repaces Pspagos  and Pspagos for  your tables  names 

    public function showAllPspagos(Pspagos $pspagos)
    {


        try {

            return response()->json($pspagos::all());
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOnePspagos(Pspagos $pspagos, $id)
    {


        try {

            return response()->json($pspagos::find($id));
        } catch (\Exception $e) {

            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }



    public function create(Request $request, Psfechaspago $psfechaspago, Pspagos $pspagos)
    {
        try {
            if (!$request->has('fecha_pago')) {
                return response()->json(['error' => 'Fecha de pago no proporcionada'], 400);
            }
            // Buscar la fecha de pago
            $fechaPago = $psfechaspago::find($request->get('id'));

            if (!$fechaPago) {
                return response()->json(['error' => 'Fecha de pago no encontrada'], 404);
            }
            // Obtener valores necesarios
            $valorCuota = $fechaPago->valor_pagar;

            // Validar que el valor de la cuota sea correcto
            if (empty($valorCuota) || $valorCuota <= 0) {
                return response()->json(['error' => 'Valor de cuota inválido'], 400);
            }
            // Obtener la fecha actual

            $fechaHora = Carbon::parse($request->get('fecha'));

            // Verificar si ya existe un pago registrado para esta fecha y préstamo
            $pagoExistente = $pspagos::where('id_fecha_pago', $request->get('id'))
                ->where('id_prestamo', $request->get('id_prestamo'))
                ->exists();
            if ($pagoExistente) {
                return response()->json(['error' => 'El pago ya ha sido registrado anteriormente'], 409);
            }

            // Registrar el pago de la cuota
            $pspagos::create([
                'fecha_pago' => $fechaPago->fecha_pago,
                'id_cliente' => $request->get('id_cliente'),
                'id_usureg' => $request->get('id_user'),
                'id_empresa' => $request->get('id_empresa'),
                'fecha_realpago' => $fechaHora,
                'id_prestamo' => $request->get('id_prestamo'),
                'id_fecha_pago' => $request->get('id'),
                'valcuota' => $valorCuota,
                'ind_estado' => 1,
                'ind_abonocapital' => 0
            ]);
            return response()->json(['success' => 'Pago registrado correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar el pago', 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id, Request $request, Pspagos $pspagos)
    {


        try {

            $data = $pspagos::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }

    public function delete($id, Pspagos $pspagos)
    {


        try {

            $pspagos::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully'), 200);
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }
}
