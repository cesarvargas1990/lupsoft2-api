<?php


namespace App\Http\Traits\General;

use DB;
use App\Psperiodopago;
use App\PsEmpresa;
use App\Pspstiposistemaprest;


trait calculadoraCuotasPrestamosTrait
{

    public function calcularCuota($request,Psperiodopago $psperiodopago, Pspstiposistemaprest $pspstiposistemaprest){
        setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
        $id_periodo_pago = $request->get('id_periodo_pago');
        $formaPago = $psperiodopago::find( $id_periodo_pago);
        $id_periodo_pago = $formaPago->id; // se usa dentro del eval
        $sistemaPrestamo =$request->get('id_sistema_pago');
        $formula =  $pspstiposistemaprest::where('codtipsistemap',$sistemaPrestamo)->first()->formula;
        $numcuotas = $request->get('numcuotas');
        $porcint = $request->get('porcint'); 
        $valorpres = $request->get('valorpres');
        $salida =  eval($formula);
        return $salida;
    }

    function generarTablaAmortizacion($request,Psperiodopago $psperiodopago,Pspstiposistemaprest $pspstiposistemaprest) {
        return $this->calcularCuota($request,$psperiodopago,$pspstiposistemaprest)['tabla_formato'];
    }
    

    function SpanishDate($FechaStamp)
    {
        $ano = date('Y',$FechaStamp);
        $mes = date('n',$FechaStamp);
        $dia = date('d',$FechaStamp);

        $interes =
        $diasemana = date('w',$FechaStamp);
        $diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
            "Jueves","Viernes","Sábado");
        $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
            "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        return $diassemanaN[$diasemana].", $dia de ". $mesesN[$mes] ." de $ano";
    }

    function adicionarFechas ($date,$id_periodo_pago) {
        $array = [
            1 => 'P1D',
            2 => 'P7D',
            3 => 'P15D',
            4 => 'P1M',
            5 => 'P1Y'
        ];
        return $date->add(new \DateInterval($array[$id_periodo_pago]))->format('Y-m-d');
    }

}