<?php

namespace Tests\Unit;

use Mockery;
use TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamosTraitTestDummy
{
    use \App\Http\Traits\General\prestamosTrait;

    /**
     * Este método se burla (mock) en el test, 
     * para simular la respuesta de calcularCuota().
     */
    public function calcularCuota($request)
    {
        // Implementación real si existiera, aquí queda vacío.
    }
}

class PrestamosTraitTest extends TestCase
{
    /**
     * Prueba unitaria de guardarPrestamoFechas().
     */
    public function testGuardarPrestamoFechas()
    {
        // 1. Prepara un Request con datos de ejemplo
        $request = Request::create('/dummy', 'POST', [
            'nitempresa'    => '123456789',
            'id_cliente'    => 10,
            'valorpres'     => 100000,
            'numcuotas'     => 2,
            'porcint'       => 5,
            'id_forma_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'fec_inicial'   => '01/01/2023',
            'id_cobrador'   => 999,
            'id_usureg'     => 100,
            'fecha'         => '2023-01-01',
        ]);

        // 2. Crea una instancia parcial (mock) de la clase dummy que usa el trait
        $dummy = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // 3. Simula la respuesta de calcularCuota()
        $dummy->shouldReceive('calcularCuota')
            ->once()
            ->with($request)
            ->andReturn([
                'datosprestamo' => [
                    'valor_cuota' => 50000
                ],
                'tabla' => [
                    [
                        'fecha'       => '2023-02-01',
                        'interes'     => 50000,
                        't_pagomes'   => 50000,
                        'ind_renovar' => 0
                    ],
                    [
                        'fecha'       => '2023-03-01',
                        'interes'     => 50000,
                        't_pagomes'   => 50000,
                        'ind_renovar' => 0
                    ],
                ]
            ]);

        // 4. Mock de la inserción principal en psprestamos
        //    Retornará un ID ficticio (ej. 1)
        DB::shouldReceive('table->insertGetId')
            ->once()
            ->withArgs(function ($data) {
                // Validamos campos relevantes
                return (
                    isset($data['valorpres']) &&
                    $data['valorpres'] === 100000 &&
                    isset($data['valcuota']) &&
                    $data['valcuota'] === 50000
                );
            })
            ->andReturn(1);

        // 5. Mock de la inserción en psfechaspago (dos veces)
        DB::shouldReceive('table->insert')
            ->times(2)
            ->withArgs(function ($insertData) {
                // Aquí podrías hacer comprobaciones adicionales si quieres
                return true;
            })
            ->andReturn(true);

        // 6. Ejecuta el método a probar
        $prestamoId = $dummy->guardarPrestamoFechas($request);

        // 7. Verifica el ID retornado
        $this->assertEquals(1, $prestamoId, 'Debe retornar el ID ficticio (1) que simulamos.');
    }

    public function testConsultaListadoPrestamos()
    {
        // 1. Define un nit_empresa de prueba
        $nit_empresa = '123456789';

        // 2. Crea un mock parcial de la clase dummy
        //    que internamente usa el trait.
        $dummy = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // 3. Simula la respuesta de obtenerQryListadoPrestamos($nit_empresa)
        //    Devolvemos un query ficticio.
        $dummy->shouldReceive('obtenerQryListadoPrestamos')
              ->once()
              ->with($nit_empresa)
              ->andReturn('SELECT * FROM psprestamos WHERE nitempresa = :nit_empresa');

        // 4. Preparamos un array de objetos que simulará la respuesta de DB::select
        $mockData = [
            (object) ['id' => 1, 'valorpres' => 1000],
            (object) ['id' => 2, 'valorpres' => 2000],
        ];

        // 5. Hacemos mock de DB::select
        DB::shouldReceive('select')
            ->once()
            ->with(
                'SELECT * FROM psprestamos WHERE nitempresa = :nit_empresa',
                ['nit_empresa' => $nit_empresa]
            )
            ->andReturn($mockData);

        // 6. Invocamos el método a probar
        $result = $dummy->consultaListadoPrestamos($nit_empresa);

        // 7. Verificamos que se devuelva el mismo array que simulamos
        $this->assertEquals($mockData, $result, 'Debe retornar los resultados de DB::select');
    }

     public function testObtenerQryListadoPrestamos()
    {
        // 1. Prepara el nit_empresa de ejemplo
        $nit_empresa = '123456789';

        // 2. Instancia la clase dummy que usa el trait (no hace falta mock porque este método no llama a DB)
        $dummy = new PrestamosTraitTestDummy();

        // 3. Ejecuta el método
        $result = $dummy->obtenerQryListadoPrestamos($nit_empresa);

        // 4. Construimos el string que esperamos 
        //    (debe coincidir exactamente con lo que retorna el método)
        $expectedQuery = "
        SELECT 
        date_format(CURDATE(),'%d/%m/%Y') fecha_actual,
        date_format(CURRENT_TIME(), '%H:%i:%s %p') hora_actua,
        pre.id id_prestamo,
        format(pre.valorpres,2) valorpresf,
        pre.*,
        cli.*,
        em.*,
        ide.*,
        tsip.*,
        pp.*,
        pp.nomperiodopago nomfpago
        FROM 
        psprestamos pre ,
        psclientes cli, 
        psempresa em, 
        pstipodocidenti ide, 
        pstiposistemaprest tsip,
        psperiodopago pp
        WHERE pre.nitempresa = :nit_empresa
        AND pre.id_cliente = cli.id
        and pre.codtipsistemap  = tsip.codtipsistemap 
        and pp.id = pre.id_forma_pago
        AND em.nitempresa = pre.nitempresa
        AND  cli.codtipdocid = ide.id
        AND pre.ind_estado = 1";

        // 5. Afirmamos que el resultado sea igual al string esperado
        $this->assertEquals($expectedQuery, $result);
    }

    public function testConsultaVariablesPrestamo()
    {
        // 1. Crea un mock parcial de la clase dummy que usa el trait
        $dummy = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // 2. Define valores de ejemplo
        $nit_empresa  = '123456789';
        $id_prestamo  = 10;

        // 3. Simula la respuesta de obtenerQryListadoPrestamos($nit_empresa)
        //    Devuelve la parte base del query; luego se espera que el método
        //    añada " and pre.id = :id_prestamo"
        $baseQuery = 'SELECT * FROM psprestamos pre WHERE pre.nitempresa = :nit_empresa AND pre.ind_estado = 1';
        $dummy->shouldReceive('obtenerQryListadoPrestamos')
            ->once()
            ->with($nit_empresa)
            ->andReturn($baseQuery);

        // 4. Prepara el query final que esperamos
        $expectedQuery = $baseQuery . ' and pre.id = :id_prestamo';

        // 5. Simulamos datos de ejemplo que DB::select retornará
        $mockData = [
            (object) ['id' => 10, 'valorpres' => 15000],
            (object) ['id' => 11, 'valorpres' => 20000],
        ];

        // 6. Mock de DB::select con la consulta y los binds esperados
        DB::shouldReceive('select')
            ->once()
            ->with($expectedQuery, [
                'nit_empresa' => $nit_empresa,
                'id_prestamo' => $id_prestamo
            ])
            ->andReturn($mockData);

        // 7. Ejecuta el método a probar
        $result = $dummy->consultaVariablesPrestamo($nit_empresa, $id_prestamo);

        // 8. Verifica que retorne exactamente el array simulado
        $this->assertEquals($mockData, $result, 'Debe retornar el resultado de DB::select');
    }


    public function testReplaceVariablesInTemplate()
    {
        // 1. Instancia de la clase dummy que usa el trait
        $dummy = new PrestamosTraitTestDummy();

        // 2. Preparamos un template con llaves que incluyan y no incluyan un símbolo $
        $template = "Hola {nombre}, tu saldo es {dinero$}, y tu fecha de corte es {fecha}.";

        // 3. Definimos el array de variables que se usarán para reemplazar
        $variables = [
            'nombre' => 'Carlos',
            'dinero' => 1500,
            'fecha'  => '2023-03-01'
        ];

        // 4. Llamamos a la función a testear
        $resultado = $dummy->replaceVariablesInTemplate($template, $variables);

        // 5. Verificamos el resultado esperado:
        //    - {nombre} => "Carlos"
        //    - {dinero$} => "1500" (se quita el '$' final para la clave "dinero")
        //    - {fecha} => "2023-03-01"
        $this->assertEquals(
            "Hola Carlos, tu saldo es 1500, y tu fecha de corte es 2023-03-01.",
            $resultado
        );
    }

    public function tesGetPlantillasDocumentos() {
        $dummy = new PrestamosTraitTestDummy();
        $mockPlantillaDocuments = [ 'nombre',
        'plantilla_html',
        'nitempresa'];
        $mock= Mockery::mock('Eloquent', 'alias:\App\Pstdocplant');
        $mock->shouldReceive('where')
         ->once()
        ->andReturn($mockPlantillaDocuments);

        $request = Request::create('/dummy', 'POST', [
            'nitempresa'    => '123456789'
        ]);

        $result = $dummy->getPlantillasDocumentos($request);

        $this->assertEquals(
            $result,
            'any'
        );
       
    }

  
}
