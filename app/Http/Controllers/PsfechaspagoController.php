<?php

namespace App\Http\Controllers;

use App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
use App\Psfechaspago;
use App\Psclientes;

use Illuminate\Http\Request;

use DB;


class PsfechaspagoController extends Controller
{
    use calculadoraCuotasPrestamosTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Generic for tables, make repaces Psfechaspago  and Psfechaspago for  your tables  names
    public function showAllPsfechaspago($id_prestamo, PsFechasPago $psfechaspago)
    {
        try {
            // Obtener las fechas de pago con su respectivo préstamo
            $fechasPago = $psfechaspago::where('id_prestamo', $id_prestamo)
                ->with([
                    'prestamo.cliente',
                    'pagos' => function ($query) {
                        $query->where('ind_abonocapital', 0);
                    }
                ])
                ->get()
                ->map(function ($fp) {
                    return [
                        'id' => $fp->id,
                        'id_cliente' => $fp->prestamo->id_cliente,
                        'id_prestamo' => $fp->prestamo->id,
                        'fecha_pago' => $this->spanishDate(strtotime($fp->fecha_pago)),
                        'valcuota' => number_format($fp->valor_cuota, 2),
                        'valtotal' => number_format($fp->valor_pagar, 2),
                        'id_fecha_pago' => optional($fp->pagos->first())->id_fecha_pago ?? null,
                        'fecha_realpago' => optional($fp->pagos->first())->fecha_realpago
                            ? $this->spanishDate(strtotime($fp->pagos->first()->fecha_realpago))
                            : 'Pendiente de pago',
                    ];
                });

            return response()->json($fechasPago);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 404);
        }
    }

    public function showOnePsfechaspago($id, Psfechaspago $psfechaspago)
    {


        try {

            return response()->json($psfechaspago::find($id));
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }



    public function create(Request $request, Psfechaspago $psfechaspago)
    {


        try {

            $data = $psfechaspago::create($request->all());

            return response()->json($data, 201);
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }

    public function update($id, Request $request, Psfechaspago $psfechaspago)
    {


        try {

            $data = $psfechaspago::findOrFail($id);
            $data->update($request->all());

            return response()->json($data, 200);
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }

    public function delete($id, Psfechaspago $psfechaspago)
    {


        try {

            $psfechaspago::findOrFail($id)->delete();
            return response(array('message' => 'Deleted Successfully'), 200);
        } catch (\Exception $e) {

            return response(["message" => $e->getMessage(), 'errorCode' => $e->getCode(), 'lineError' => $e->getLine(), 'file' => $e->getFile()], 404);
        }
    }
}
