<?php

namespace Tests\Unit;

use App\Http\Controllers\PsformapagoController;
use App\Psperiodopago;
use App\Pstdocplant;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PsformapagoControllerTest extends TestCase
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

    public function test_show_psformapago_returns_data()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('get')->with(['id as value', 'nomperiodopago as label'])->andReturn([
            ['value' => 1, 'label' => 'Mensual']
        ]);

        $controller = new PsformapagoController();
        $response = $controller->ShowPsformapago(1, new Psperiodopago());

        $this->assertEquals(200, $response->getStatusCode());
    }


    public function test_show_psformapago_handles_exception()
    {
        // Mock del alias para interceptar Psperiodopago::get()
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('get')
            ->andThrow(new \Exception('DB Error', 500));
        $controller = new PsformapagoController();
        $response = $controller->ShowPsformapago(1, $mock);
        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('DB Error', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }


    public function test_consulta_tipo_doc_plantilla_returns_data()
    {
        $request = new Request(['id_empresa' => 123]);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('where')->with('id_empresa', 123)->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['id' => 1, 'nombre' => 'Plantilla 1']
        ]);

        $controller = new PsformapagoController();
        $response = $controller->consultaTipoDocPlantilla($request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_consulta_tipo_doc_plantilla_handles_exception()
    {
        $request = new Request(['id_empresa' => 123]);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('where')->andThrow(new \Exception('Consulta error', 500));

        $controller = new PsformapagoController();
        $response = $controller->consultaTipoDocPlantilla($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
