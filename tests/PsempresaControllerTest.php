<?php

namespace Tests\Unit;

use App\Http\Controllers\PsempresaController;
use App\PsEmpresa;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Illuminate\Http\JsonResponse;

class PsempresaControllerTest extends TestCase
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
        $mockController = Mockery::mock(PsempresaController::class)->makePartial();
        $mockController->shouldReceive('middleware')->once()->with('auth')->andReturnNull();
        $mockController->__construct();
        $this->assertTrue(true);
    }

    public function test_show_one_psempresa_returns_data_correctly()
    {
        $empresa = (object)['id' => 1, 'nombre' => 'Empresa Demo'];

        $psempresaMock = Mockery::mock(PsEmpresa::class);
        $psempresaMock->shouldReceive('where')
            ->with('id', 1)
            ->once()
            ->andReturnSelf();
        $psempresaMock->shouldReceive('first')
            ->once()
            ->andReturn($empresa);

        $controller = new PsempresaController();
        $response = $controller->showOnePsempresa( $psempresaMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Empresa Demo', $data['nombre']);
    }

    public function test_show_one_psempresa_handles_exception()
    {
        $psempresaMock = Mockery::mock(PsEmpresa::class);
        $psempresaMock->shouldReceive('where')
            ->withAnyArgs()
            ->andThrow(new \Exception('Fallo en la base de datos', 500));

        $controller = new PsempresaController();
        $response = $controller->showOnePsempresa($psempresaMock,1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Fallo en la base de datos', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }

    public function test_update_psempresa_successfully()
    {
        $request = new Request([
            'nit' => '123456789',
            'nombre' => 'Empresa Actualizada'
        ]);

        $empresaMock = Mockery::mock();
        $empresaMock->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $psempresaMock = Mockery::mock(PsEmpresa::class);
        $psempresaMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andReturn($empresaMock);

        $controller = new PsempresaController();
        $response = $controller->update(1, $request, $psempresaMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_psempresa_handles_exception()
    {
        $request = new Request(['nit' => '123456789']);

        $psempresaMock = Mockery::mock(PsEmpresa::class);
        $psempresaMock->shouldReceive('findOrFail')
            ->with(1)
            ->once()
            ->andThrow(new \Exception('Error actualizando', 500));

        $controller = new PsempresaController();
        $response = $controller->update(1, $request, $psempresaMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Error actualizando', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }
}
