<?php

namespace Tests\Unit;

use App\Http\Controllers\PsclientesController;
use App\Psclientes;
use App\Psfechaspago;
use App\Pspagos;
use App\Psprestamos;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PsclientesControllerTest extends TestCase
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

    public function test_show_all_psclientes_successfully()
    {
        $mock = Mockery::mock(Psclientes::class);
        $mock->shouldReceive('where')->with('id_empresa', 1)->andReturnSelf();
        $mock->shouldReceive('where')->with('ind_estado', 1)->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([['id' => 1]]);

        $controller = new PsclientesController();
        $response = $controller->showAllPsclientes(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_psclientes_successfully()
    {
        $mock = Mockery::mock(Psclientes::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        $controller = new PsclientesController();
        $response = $controller->showOnePsclientes(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_psclientes_not_found()
    {
        $mock = Mockery::mock(Psclientes::class);
        $mock->shouldReceive('find')->with(1)->andReturn(null);

        $controller = new PsclientesController();
        $response = $controller->showOnePsclientes(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_select_psclientes_successfully()
    {
        $mock = Mockery::mock(Psclientes::class);
        $mock->shouldReceive('select')->with('id as value', 'nomcliente as label')->andReturnSelf();
        $mock->shouldReceive('where')->with('id_empresa', 1)->andReturnSelf();
        $mock->shouldReceive('where')->with('ind_estado', 1)->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([]);

        $controller = new PsclientesController();
        $response = $controller->ShowPsclientes($mock, 1);

        $this->assertEquals(200, $response->getStatusCode());
    }


    public function test_delete_psclientes_successfully()
    {
        $cliente = Mockery::mock();
        $cliente->shouldReceive('update')->once()->andReturn(true);

        $psclientes = Mockery::mock(Psclientes::class);
        $psclientes->shouldReceive('findOrFail')->with(1)->andReturn($cliente);

        $psprestamos = Mockery::mock(Psprestamos::class);
        $psprestamos->shouldReceive('where')->with(['id_cliente' => 1])->andReturnSelf();
        $psprestamos->shouldReceive('update')->with(['ind_estado' => 0])->andReturn(true);

        $pspagos = Mockery::mock(Pspagos::class);
        $pspagos->shouldReceive('where')->with(['id_cliente' => 1])->andReturnSelf();
        $pspagos->shouldReceive('update')->with(['ind_estado' => 0])->andReturn(true);

        $psfechaspago = Mockery::mock(Psfechaspago::class);
        $psfechaspago->shouldReceive('where')->with(['id_cliente' => 1])->andReturnSelf();
        $psfechaspago->shouldReceive('update')->with(['ind_estado' => 0])->andReturn(true);

        $controller = new PsclientesController();
        $response = $controller->delete(1, $psclientes, $psprestamos, $pspagos, $psfechaspago);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_psclientes_handles_exception()
    {
        $mockPsclientes = Mockery::mock(Psclientes::class);
        $mockPsclientes->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Error al eliminar cliente', 500));

        $mockPsprestamos = Mockery::mock(Psprestamos::class);
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPsfechaspago = Mockery::mock(Psfechaspago::class);

        $controller = new PsclientesController();
        $response = $controller->delete(1, $mockPsclientes, $mockPsprestamos, $mockPspagos, $mockPsfechaspago);

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertEquals('Error al eliminar cliente', $json['message']);
        $this->assertEquals(500, $json['errorCode']);
    }

    
}
