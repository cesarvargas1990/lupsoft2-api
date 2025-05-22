<?php

namespace Tests\Unit;

use App\Http\Controllers\PspagosController;
use App\Pspagos;
use App\Psfechaspago;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Carbon\Carbon;

class PspagosControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_applies_auth_middleware()
    {
        $mockController = Mockery::mock(PspagosController::class)->makePartial();
        $mockController->shouldReceive('middleware')->once()->with('auth')->andReturnNull();
        $mockController->__construct();
        $this->assertTrue(true);
    }

    public function test_show_all_pspagos_successfully()
    {
        $mock = Mockery::mock(Pspagos::class);
        $mock->shouldReceive('all')->once()->andReturn([['id' => 1]]);

        $controller = new PspagosController();
        $response = $controller->showAllPspagos($mock);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([['id' => 1]], json_decode($response->getContent(), true));
    }

    public function test_show_one_pspago_successfully()
    {
        $mock = Mockery::mock(Pspagos::class);
        $mock->shouldReceive('find')->with(1)->once()->andReturn((object) ['id' => 1]);

        $controller = new PspagosController();
        $response = $controller->showOnePspagos($mock, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 1], json_decode($response->getContent(), true));
    }

    public function test_create_pspago_successfully()
    {
        $mockFecha = Mockery::mock(Psfechaspago::class);
        $mockPagos = Mockery::mock(Pspagos::class);

        $fechaPago = (object) [
            'valor_pagar' => 150000,
            'fecha_pago' => '2025-03-31'
        ];

        $mockFecha->shouldReceive('find')->with(10)->andReturn($fechaPago);
        $mockPagos->shouldReceive('where')->andReturnSelf();
        $mockPagos->shouldReceive('where')->andReturnSelf();
        $mockPagos->shouldReceive('exists')->andReturn(false);
        $mockPagos->shouldReceive('create')->once()->andReturnTrue();

        $request = new Request([
            'id' => 10,
            'id_cliente' => 1,
            'id_user' => 1,
            'id_empresa' => 1,
            'id_prestamo' => 99,
            'valor_pago' => 150000,
            'fecha_pago' => '2025-03-31',
            'fecha' => Carbon::now()->toDateTimeString()
        ]);

        $controller = new PspagosController();
        $response = $controller->create($request, $mockFecha, $mockPagos);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Pago registrado correctamente', json_decode($response->getContent(), true)['success']);
    }


    public function test_create_pspago_fails_if_exists()
    {
        $mockFecha = Mockery::mock(Psfechaspago::class);
        $mockPagos = Mockery::mock(Pspagos::class);

        $mockFecha->shouldReceive('find')->with(10)->andReturn((object) [
            'valor_pagar' => 100000,
            'fecha_pago' => '2025-03-31'
        ]);

        $mockPagos->shouldReceive('where')->andReturnSelf();
        $mockPagos->shouldReceive('where')->andReturnSelf();
        $mockPagos->shouldReceive('exists')->andReturn(true);

        $request = new Request([
            'id' => 10,
            'id_prestamo' => 1,
            'fecha_pago' => '2025-03-31'
        ]);

        $controller = new PspagosController();
        $response = $controller->create($request, $mockFecha, $mockPagos);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('El pago ya ha sido registrado anteriormente', json_decode($response->getContent(), true)['error']);
    }

    public function test_update_pspago_successfully()
    {
        $mockEntity = Mockery::mock();
        $mockEntity->shouldReceive('update')->once()->andReturn(true);

        $mock = Mockery::mock(Pspagos::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($mockEntity);

        $request = new Request(['valcuota' => 9999]);

        $controller = new PspagosController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_pspago_successfully()
    {
        $mockEntity = Mockery::mock();
        $mockEntity->shouldReceive('delete')->once();
        $mock = Mockery::mock(Pspagos::class);
        $mock->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockEntity);
        $controller = new PspagosController();
        $response = $controller->delete(1, $mock);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Deleted Successfully', json_decode($response->getContent(), true)['message']);
    }

    public function test_delete_pspago_handles_exception()
    {
        $pspagosMock = Mockery::mock(Pspagos::class);
        $pspagosMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('No se pudo eliminar', 123));

        $controller = new PspagosController();
        $response = $controller->delete(1, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('No se pudo eliminar', $data['message']);
        $this->assertEquals(123, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_update_pspago_handles_exception()
    {
        $request = new \Illuminate\Http\Request([
            'campo1' => 'valor1'
        ]);

        $pspagosMock = Mockery::mock(\App\Pspagos::class);
        $pspagosMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Fallo al actualizar', 777));

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->update(1, $request, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Fallo al actualizar', $data['message']);
        $this->assertEquals(777, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_show_all_pspagos_handles_exception()
    {
        $mock = Mockery::mock(Pspagos::class);
        $mock->shouldReceive('all')
            ->once()
            ->andThrow(new \Exception('Error al obtener pagos', 500));

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->showAllPspagos($mock);

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Error al obtener pagos', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_show_one_pspago_handles_exception()
    {
        $pspagosMock = Mockery::mock(\App\Pspagos::class);
        $pspagosMock->shouldReceive('find')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Error al buscar pago', 777));

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->showOnePspagos($pspagosMock, 1);

        $data = $response->getOriginalContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Error al buscar pago', $data['message']);
        $this->assertEquals(777, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_create_pspago_handles_general_exception()
    {
        $request = new \Illuminate\Http\Request([
            'fecha_pago' => '2025-03-31',
            'id' => 1,
            'id_prestamo' => 123,
            'fecha' => '2025-03-31',
            'id_cliente' => 45,
            'id_user' => 99,
            'id_empresa' => 2
        ]);

        $psfechaspagoMock = Mockery::mock(\App\Psfechaspago::class);
        $psfechaspagoMock->shouldReceive('find')
            ->with(1)
            ->andThrow(new \Exception('Error inesperado en find', 1234));

        $pspagosMock = Mockery::mock(\App\Pspagos::class);

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->create($request, $psfechaspagoMock, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('Error al registrar el pago', $data['error']);
        $this->assertEquals('Error inesperado en find', $data['message']);
    }

    public function test_create_pspago_fails_when_fecha_pago_not_provided()
    {
        $request = new \Illuminate\Http\Request([
            // No se incluye 'fecha_pago'
            'id' => 1,
            'fecha' => '2025-04-01',
            'id_cliente' => 10,
            'id_user' => 1,
            'id_empresa' => 1,
            'id_prestamo' => 5
        ]);

        $psfechaspagoMock = Mockery::mock(\App\Psfechaspago::class);
        $pspagosMock = Mockery::mock(\App\Pspagos::class);

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->create($request, $psfechaspagoMock, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Fecha de pago no proporcionada', $data['error']);
    }

    public function test_create_pspago_fails_when_fecha_pago_not_found()
    {
        $request = new \Illuminate\Http\Request([
            'fecha_pago' => true,
            'id' => 1,
            'fecha' => '2025-04-01',
            'id_cliente' => 10,
            'id_user' => 1,
            'id_empresa' => 1,
            'id_prestamo' => 5
        ]);

        $psfechaspagoMock = Mockery::mock(\App\Psfechaspago::class);
        $psfechaspagoMock->shouldReceive('find')->with(1)->andReturn(null);

        $pspagosMock = Mockery::mock(\App\Pspagos::class);

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->create($request, $psfechaspagoMock, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Fecha de pago no encontrada', $data['error']);
    }

    public function test_create_pspago_fails_when_valor_cuota_invalido()
    {
        $request = new \Illuminate\Http\Request([
            'fecha_pago' => true,
            'id' => 1,
            'fecha' => '2025-04-01',
            'id_cliente' => 10,
            'id_user' => 1,
            'id_empresa' => 1,
            'id_prestamo' => 5
        ]);

        $fechaPago = (object) ['valor_pagar' => 0];

        $psfechaspagoMock = Mockery::mock(\App\Psfechaspago::class);
        $psfechaspagoMock->shouldReceive('find')->with(1)->andReturn($fechaPago);

        $pspagosMock = Mockery::mock(\App\Pspagos::class);

        $controller = new \App\Http\Controllers\PspagosController();
        $response = $controller->create($request, $psfechaspagoMock, $pspagosMock);

        $data = $response->getOriginalContent();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Valor de cuota inválido', $data['error']);
    }
}
