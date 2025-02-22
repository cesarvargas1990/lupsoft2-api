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

    /**
     * Cierra Mockery después de cada test.
     */
  
}
