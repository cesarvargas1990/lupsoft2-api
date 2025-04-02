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
        $mock->shouldReceive('find')->with(1)->once()->andReturn((object)['id' => 1]);

        $controller = new PspagosController();
        $response = $controller->showOnePspagos($mock, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 1], json_decode($response->getContent(), true));
    }

    public function test_create_pspago_successfully()
    {
        $mockFecha = Mockery::mock(Psfechaspago::class);
        $mockPagos = Mockery::mock(Pspagos::class);

        $fechaPago = (object)[
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

        $mockFecha->shouldReceive('find')->with(10)->andReturn((object)[
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

        $mock = Mockery::mock('alias:App\\Pspagos');
        $mock->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockEntity);

        $controller = new PspagosController();
        $response = $controller->delete(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Deleted Successfully', json_decode($response->getContent(), true)['message']);
    }
}
