<?php

namespace Tests\Unit;

use App\Http\Controllers\CuotasController;
use App\Psperiodopago;
use App\Pspstiposistemaprest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Laravel\Lumen\Testing\TestCase;
use Illuminate\Http\JsonResponse;

class CuotasControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }


    public function test_controller_applies_auth_middleware()
    {
        // Crear un mock del controlador
        $mockController = Mockery::mock(CuotasController::class)->makePartial();

        // Simular la llamada al constructor para verificar si el middleware fue registrado
        $mockController->shouldReceive('middleware')
            ->once()
            ->with('auth')
            ->andReturnNull();

        // Llamar manualmente al constructor para comprobar la aplicación del middleware
        $mockController->__construct();

        // Si llega hasta aquí, significa que no hubo errores y el middleware se aplica correctamente
        $this->assertTrue(true);
    }
    public function test_calcular_cuotas_returns_correct_response()
    {
        // Simular el request
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'numcuotas' => 12,
            'porcint' => 5,
            'valorpres' => 100000
        ]);

        // Simular el modelo Psperiodopago
        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);

        // Simular el modelo Pspstiposistemaprest
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);

        // Simular el comportamiento de generarTablaAmortizacion
        $mockController = Mockery::mock(CuotasController::class)->makePartial();
        $mockController->shouldReceive('generarTablaAmortizacion')
            ->with($request, $mockPsperiodopago, $mockPspstiposistemaprest)
            ->andReturn([
                'tabla_formato' => [
                    ['cuota' => 1, 'valor' => 10000],
                    ['cuota' => 2, 'valor' => 10000]
                ]
            ]);

        // Ejecutar la función
        $response = $mockController->calcularCuotas($request, $mockPsperiodopago, $mockPspstiposistemaprest);

        // Verificar que la respuesta sea un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        
        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, $responseData['tabla_formato']);
        $this->assertEquals(10000, $responseData['tabla_formato'][0]['valor']);
    }

    public function test_calcular_cuotas_handles_exception()
    {
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01'
        ]);

        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);

        $mockController = Mockery::mock(CuotasController::class)->makePartial();
        $mockController->shouldReceive('generarTablaAmortizacion')
            ->withAnyArgs()
            ->andThrow(new \Exception('Error en la base de datos', 500));

        $response = $mockController->calcularCuotas($request, $mockPsperiodopago, $mockPspstiposistemaprest);
        
        // Asegurar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        
        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);
        
        // Verificar los datos de error
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    public function test_calcular_cuotas2_returns_correct_response()
    {
        // Simular el request
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'numcuotas' => 12,
            'porcint' => 5,
            'valorpres' => 100000
        ]);

        // Simular el modelo Psperiodopago
        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPsperiodopago->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        // Simular el modelo Pspstiposistemaprest
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);
        $mockPspstiposistemaprest->shouldReceive('where')->with('codtipsistemap', 'SIS01')->andReturnSelf();
        $mockPspstiposistemaprest->shouldReceive('first')->andReturn((object)['formula' => 'return ["cuota" => 10000, "tabla_formato" => []];']);

        // Simular el comportamiento del controlador
        $mockController = Mockery::mock(CuotasController::class)->makePartial();
        
        // Ejecutar la función
        $response = $mockController->calcularCuotas2($request, $mockPsperiodopago, $mockPspstiposistemaprest);

        // Verificar que la respuesta sea un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        
        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('cuota', $responseData);
        $this->assertEquals(10000, $responseData['cuota']);
    }

    public function test_calcular_cuotas2_handles_exception()
    {
        $request = new Request([
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01'
        ]);

        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);

        $mockController = Mockery::mock(CuotasController::class)->makePartial();
        $mockController->shouldReceive('calcularCuota')
            ->withAnyArgs()
            ->andThrow(new \Exception('Error en la base de datos', 500));

        $response = $mockController->calcularCuotas2($request, $mockPsperiodopago, $mockPspstiposistemaprest);
        
        // Asegurar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);
        
        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);
        
        // Verificar los datos de error
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    
}
