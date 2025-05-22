<?php

namespace Tests\Unit;

use App\Http\Controllers\PstdocadjuntosController;
use App\Pstdocadjuntos;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PstdocadjuntosControllerTest extends TestCase
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

    public function test_show_all_returns_data()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('all')->once()->andReturn([
            ['id' => 1, 'nombre' => 'Documento 1']
        ]);

        $controller = new PstdocadjuntosController();
        $response = $controller->showAllPstdocadjuntos($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_all_handles_exception()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('all')->andThrow(new \Exception('DB error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->showAllPstdocadjuntos($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_returns_data()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object) ['id' => 1]);

        $controller = new PstdocadjuntosController();
        $response = $controller->showOnePstdocadjuntos(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_handles_exception()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('find')->andThrow(new \Exception('Error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->showOnePstdocadjuntos(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_select_returns_data()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('where')->with('id_empresa', 1)->andReturnSelf();
        $mock->shouldReceive('select')->with('id as value', 'nombre as label')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['value' => 1, 'label' => 'Documento 1']
        ]);

        $controller = new PstdocadjuntosController();
        $response = $controller->ShowPstdocadjuntos(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_select_handles_exception()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('where')->andThrow(new \Exception('Error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->ShowPstdocadjuntos(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    // public function test_create_successfully()
    // {
    //     $request = new Request(['campo' => 'valor']);

    //     $mock = Mockery::mock(Pstdocadjuntos::class);
    //     $mock->shouldReceive('create')->with($request->all())->andReturn((object)['id' => 1]);

    //     $controller = new PstdocadjuntosController();
    //     $response = $controller->create($request, $mock);

    //     $this->assertEquals(201, $response->getStatusCode());
    // }

    public function test_create_handles_exception()
    {
        $request = new Request(['campo' => 'valor']);

        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_successfully()
    {
        $request = new Request(['campo' => 'nuevo valor']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PstdocadjuntosController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    // public function test_delete_successfully()
    // {
    //     $model = Mockery::mock();
    //     $model->shouldReceive('delete')->andReturn(true);

    //     $mock = Mockery::mock(Pstdocadjuntos::class);
    //     $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

    //     $controller = new PstdocadjuntosController();
    //     $response = $controller->delete(1, $mock);

    //     $this->assertEquals(200, $response->getStatusCode());
    // }

    public function test_delete_handles_exception()
    {
        $mock = Mockery::mock(Pstdocadjuntos::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete error', 500));

        $controller = new PstdocadjuntosController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
