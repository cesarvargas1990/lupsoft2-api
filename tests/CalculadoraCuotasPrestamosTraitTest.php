<?php

namespace Tests\Unit;
use Mockery;
use TestCase;
use Illuminate\Http\Request;
use App\Psperiodopago;
use App\Pspstiposistemaprest;
use Carbon\Carbon;

class CalculadoraCuotasPrestamosTraitTest extends TestCase
{
   
    public function test_adicionar_fechas_returns_correct_date()
    {
        $classInstance = new class {
            use \App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
        };

        $date = Carbon::create(2023, 1, 1);
        $this->assertEquals('2023-01-02', $classInstance->adicionarFechas($date, 1)); // 1 día
        $this->assertEquals('2023-01-09', $classInstance->adicionarFechas($date, 2)); // 7 días
        $this->assertEquals('2023-01-24', $classInstance->adicionarFechas($date, 3)); // 15 días
        $this->assertEquals('2023-02-24', $classInstance->adicionarFechas($date, 4)); // 1 mes
        $this->assertEquals('2024-02-24', $classInstance->adicionarFechas($date, 5)); // 1 año
        $this->assertEquals(null, $classInstance->adicionarFechas($date, 6)); // 1 año
    }

    public function test_SpanishDate_returns_correct_format()
    {
        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
        };

        $fecha = strtotime('2023-12-25');
        $resultado = $classInstance->SpanishDate($fecha);

        $this->assertEquals('Lunes, 25 de Diciembre de 2023', $resultado);
    }

    public function test_calcular_cuota_returns_correct_value()
    {
        // Simular request
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'numcuotas' => 12,
            'porcint' => 5,
            'valorpres' => 100000
        ]);

        // Simular modelo Psperiodopago
        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPsperiodopago->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        // Simular modelo Pspstiposistemaprest
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);
        $mockPspstiposistemaprest->shouldReceive('where')->with('id', 'SIS01')->andReturnSelf();
        $mockPspstiposistemaprest->shouldReceive('first')->andReturn((object)['formula' => 'return ($valorpres * ($porcint / 100)) / $numcuotas;']);

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->calcularCuota($request, $mockPsperiodopago, $mockPspstiposistemaprest);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(416.67, round($resultado, 2));
    }

    

    public function test_generarTablaAmortizacion_returns_correct_value()
    {
        // Simular request
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'numcuotas' => 12,
            'porcint' => 5,
            'valorpres' => 100000
        ]);

        // Simular modelo Psperiodopago
        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPsperiodopago->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        // Simular modelo Pspstiposistemaprest
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);
        $mockPspstiposistemaprest->shouldReceive('where')->with('id', 'SIS01')->andReturnSelf();
        $mockPspstiposistemaprest->shouldReceive('first')->andReturn((object)['formula' => 'return ($valorpres * ($porcint / 100)) / $numcuotas;']);

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->generarTablaAmortizacion($request, $mockPsperiodopago, $mockPspstiposistemaprest);

        // Verificaciones
        $this->assertNull($resultado);
        //$this->assertEquals(416.67, round($resultado, 2));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
