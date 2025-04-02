<?php

namespace Tests\Unit;

use App\Http\Controllers\PsperiodopagoController;
use App\Psperiodopago;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class PsperiodopagoControllerTest extends TestCase
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
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('all')->once()->andReturn([
            ['id' => 1, 'nombre' => 'Mensual'],
            ['id' => 2, 'nombre' => 'Quincenal']
        ]);

        $controller = new PsperiodopagoController();
        $response = $controller->showAllPsperiodopago($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_all_handles_exception()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('all')->andThrow(new \Exception('DB error', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->showAllPsperiodopago($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_returns_data()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        $controller = new PsperiodopagoController();
        $response = $controller->showOnePsperiodopago(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_handles_exception()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('find')->andThrow(new \Exception('Error buscando', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->showOnePsperiodopago(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_select_returns_data()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('select')->with('id as value', 'nomperiodopago as label')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['value' => 1, 'label' => 'Mensual']
        ]);

        $controller = new PsperiodopagoController();
        $response = $controller->ShowPsperiodopago($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_select_handles_exception()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('select')->andThrow(new \Exception('Select error', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->ShowPsperiodopago($mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_successfully()
    {
        $request = new Request(['nombre' => 'Trimestral']);

        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('create')->with($request->all())->andReturn((object)['id' => 3]);

        $controller = new PsperiodopagoController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Insert failed', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_successfully()
    {
        $request = new Request(['nombre' => 'Actualizado']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsperiodopagoController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update error', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_successfully()
    {
        $model = Mockery::mock();
        $model->shouldReceive('delete')->andReturn(true);

        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsperiodopagoController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_handles_exception()
    {
        $mock = Mockery::mock(Psperiodopago::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete error', 500));

        $controller = new PsperiodopagoController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
