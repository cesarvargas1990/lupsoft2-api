<?php

namespace Tests;


use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Psprestamos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use App\Http\Traits\General\prestamosTrait;
use Mockery;

class PrestamosTraitTest extends BaseTestCase
{
    use DatabaseTransactions;
    use prestamosTrait; // Usar el trait para pruebas

    /** @test */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $this->assertInstanceOf(\Laravel\Lumen\Application::class, $app);
        return $app;
    }

    public function testCreateApplication()
    {
        $app = $this->createApplication();
        $this->assertInstanceOf(\Laravel\Lumen\Application::class, $app);
    }

    public function test_it_can_create_prestamo_and_return_id()
    {
        // Simular datos del request
        $requestData = [
            'nitempresa' => '123456789',
            'id_cliente' => 1,
            'valorpres' => 1000,
            'numcuotas' => 10,
            'porcint' => 5,
            'id_forma_pago' => 1,
            'id_sistema_pago' => 1,
            'fec_inicial' => Carbon::now()->format('Y-m-d'),
            'id_cobrador' => 1,
            'id_usureg' => 1,
            'fecha' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        $request = new Request($requestData);

        // Crear un mock del trait
        $mockTrait = Mockery::mock(prestamosTrait::class)->makePartial();
        $mockTrait->shouldReceive('calcularCuota')
            ->once()
            ->with(Mockery::on(function($arg) use ($requestData) {
                return $arg->all() == $requestData;
            }))
            ->andReturn([
                'datosprestamo' => ['valor_cuota' => 100],
                'tabla' => [
                    ['fecha' => '2025-02-10', 'interes' => 10, 't_pagomes' => 110, 'ind_renovar' => 0],
                    ['fecha' => '2025-03-10', 'interes' => 10, 't_pagomes' => 110, 'ind_renovar' => 0]
                ]
            ]);

        // Llamar la función testada
        $prestamoId = $mockTrait->guardarPrestamoFechas($request);

        // Validar que el préstamo fue creado
        $this->assertNotNull($prestamoId);
        
        // Verificar manualmente en la base de datos en lugar de assertDatabaseHas
        $prestamo = DB::table('psprestamos')->where('id', $prestamoId)->where('nitempresa', '123456789')->first();
        $this->assertNotNull($prestamo, 'El préstamo no se encontró en la base de datos.');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
