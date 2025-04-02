<?php

namespace Tests\Unit;

use App\Http\Controllers\PstdocplantController;
use App\Pstdocplant;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PstdocplantControllerTest extends TestCase
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

    public function test_show_all_pstdocplant_returns_data()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('all')->once()->andReturn([
            ['id' => 1, 'nombre' => 'Plantilla 1'],
            ['id' => 2, 'nombre' => 'Plantilla 2']
        ]);

        $controller = new PstdocplantController();
        $response = $controller->showAllPstdocplant($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_all_pstdocplant_handles_exception()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('all')->andThrow(new \Exception('DB Error', 500));

        $controller = new PstdocplantController();
        $response = $controller->showAllPstdocplant($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_pstdocplant_returns_data()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        $controller = new PstdocplantController();
        $response = $controller->showOnePstdocplant(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_pstdocplant_handles_exception()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('find')->andThrow(new \Exception('Error buscando', 500));

        $controller = new PstdocplantController();
        $response = $controller->showOnePstdocplant(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_select_pstdocplant_returns_data()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('select')->with('codtipdocid as value', 'nomtipodocumento as label')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['value' => 'DOC01', 'label' => 'Cédula']
        ]);

        $controller = new PstdocplantController();
        $response = $controller->ShowPstdocplant($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_select_pstdocplant_handles_exception()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('select')->andThrow(new \Exception('Select error', 500));

        $controller = new PstdocplantController();
        $response = $controller->ShowPstdocplant($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_pstdocplant_successfully()
    {
        $request = new Request(['campo' => 'valor']);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('create')->with($request->all())->andReturn((object)['id' => 1]);

        $controller = new PstdocplantController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_pstdocplant_handles_exception()
    {
        $request = new Request(['campo' => 'valor']);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Create error', 500));

        $controller = new PstdocplantController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_pstdocplant_successfully()
    {
        $request = new Request(['campo' => 'nuevo valor']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PstdocplantController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_pstdocplant_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update error', 500));

        $controller = new PstdocplantController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_pstdocplant_successfully()
    {
        $model = Mockery::mock();
        $model->shouldReceive('delete')->andReturn(true);

        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PstdocplantController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_pstdocplant_handles_exception()
    {
        $mock = Mockery::mock(Pstdocplant::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete error', 500));

        $controller = new PstdocplantController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
