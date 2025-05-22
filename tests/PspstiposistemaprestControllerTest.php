<?php

namespace Tests\Unit;

use App\Http\Controllers\PspstiposistemaprestController;
use App\Pspstiposistemaprest;
use App\Psperiodopago;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PspstiposistemaprestControllerTest extends TestCase
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
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('all')->once()->andReturn([]);

        $controller = new PspstiposistemaprestController();
        $response = $controller->showAll($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_all_handles_exception()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('all')->andThrow(new \Exception('DB Error', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->showAll($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_returns_data()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object) ['id' => 1]);

        $controller = new PspstiposistemaprestController();
        $response = $controller->showOne(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_handles_exception()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('find')->andThrow(new \Exception('Not Found', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->showOne(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_empresa_select_returns_data()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('where')->with('id_empresa', 1)->andReturnSelf();
        $mock->shouldReceive('get')->with(['id as value', 'nomperiodopago as label'])->andReturn([]);

        $controller = new PspstiposistemaprestController();
        $response = $controller->Show(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_empresa_select_handles_exception()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('where')->andThrow(new \Exception('Error', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->Show(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_successfully()
    {
        $request = new Request(['field' => 'value']);
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('create')->with($request->all())->andReturn((object) ['id' => 1]);

        $controller = new PspstiposistemaprestController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_handles_exception()
    {
        $request = new Request(['field' => 'value']);
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Create failed', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_successfully()
    {
        $request = new Request(['field' => 'updated']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PspstiposistemaprestController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_handles_exception()
    {
        $request = new Request();
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update failed', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_successfully()
    {
        $model = Mockery::mock();
        $model->shouldReceive('delete')->andReturn(true);

        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PspstiposistemaprestController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_handles_exception()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete failed', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_list_select_returns_data()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('select')->with('codtipsistemap as value', 'nomtipsistemap as label')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([]);

        $controller = new PspstiposistemaprestController();
        $response = $controller->list($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_list_select_handles_exception()
    {
        $mock = Mockery::mock(Pspstiposistemaprest::class);
        $mock->shouldReceive('select')->andThrow(new \Exception('Select failed', 500));

        $controller = new PspstiposistemaprestController();
        $response = $controller->list($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
