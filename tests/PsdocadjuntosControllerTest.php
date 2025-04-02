<?php

namespace Tests\Unit;

use App\Http\Controllers\PsdocadjuntosController;
use App\Psdocadjuntos;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PsdocadjuntosControllerTest extends TestCase
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

    public function test_show_all_psdocadjuntos_returns_all_data()
    {
        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('all')->once()->andReturn([
            ['id' => 1, 'nombre' => 'doc1'],
            ['id' => 2, 'nombre' => 'doc2']
        ]);

        $controller = new PsdocadjuntosController();
        $response = $controller->showAllPstdocadjuntos($mock);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(2, json_decode($response->getContent(), true));
    }

    public function test_show_all_psdocadjuntos_handles_exception()
    {
        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('all')->once()->andThrow(new \Exception('DB error', 500));

        $controller = new PsdocadjuntosController();
        $response = $controller->showAllPstdocadjuntos($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_psdocadjuntos_returns_data()
    {
        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('where')->with('id_cliente', 5)->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['id' => 1, 'id_cliente' => 5]
        ]);

        $controller = new PsdocadjuntosController();
        $response = $controller->showOnePsdocadjuntos(5, $mock);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(5, $data[0]['id_cliente']);
    }

    public function test_show_one_psdocadjuntos_handles_exception()
    {
        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('where')->andThrow(new \Exception('Query error', 500));

        $controller = new PsdocadjuntosController();
        $response = $controller->showOnePsdocadjuntos(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_psdocadjuntos_successfully()
    {
        $request = new Request([
            'id_cliente' => 10,
            'archivo' => 'document.pdf'
        ]);

        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('create')->with($request->all())->andReturn((object) ['id' => 1]);

        $controller = new PsdocadjuntosController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_psdocadjuntos_handles_exception()
    {
        $request = new Request(['id_cliente' => 10]);

        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Insert error', 500));

        $controller = new PsdocadjuntosController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_psdocadjuntos_successfully()
    {
        $request = new Request(['archivo' => 'updated.pdf']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsdocadjuntosController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_psdocadjuntos_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update error', 500));

        $controller = new PsdocadjuntosController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_psdocadjuntos_successfully()
    {
        $model = Mockery::mock();
        $model->shouldReceive('delete')->andReturn(true);

        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsdocadjuntosController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_psdocadjuntos_handles_exception()
    {
        $mock = Mockery::mock(Psdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete error', 500));

        $controller = new PsdocadjuntosController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
