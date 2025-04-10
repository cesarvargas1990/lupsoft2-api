<?php

namespace Tests\Unit;

use App\Http\Controllers\PsclientesController;
use App\Psclientes;
use App\Psprestamos;
use App\Pspagos;
use App\Psfechaspago;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Illuminate\Http\JsonResponse;

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

    public function test_controller_applies_auth_middleware()
    {
        $mockController = Mockery::mock(PsclientesController::class)->makePartial();
        $mockController->shouldReceive('middleware')->once()->with('auth')->andReturnNull();
        $mockController->__construct();
        $this->assertTrue(true);
    }

    public function test_show_one_cliente_returns_data_correctly()
    {
        $cliente = (object)['id' => 1, 'nomcliente' => 'Cliente de Prueba'];

        $psclienteMock = Mockery::mock(Psclientes::class);
        $psclienteMock->shouldReceive('find')->with(1)->andReturn((object)[
            'nomcliente' => 'cesar vargas',
        ])->andReturnSelf();
       

        $controller = new PsclientesController();
        $response = $controller->showOnePsclientes( 1,$psclienteMock);
        fwrite(STDERR, print_r($response->getContent(), true));
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);
       
        $this->assertEquals(200, $response->getStatusCode());
      
        $this->assertEquals('Cliente de Prueba', $data['nomcliente']);
    }
}
