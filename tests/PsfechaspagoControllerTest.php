<?php

namespace Tests\Unit;

use App\Http\Controllers\PsfechaspagoController;
use App\Psfechaspago;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Carbon\Carbon;
class PsfechaspagoControllerTest extends TestCase
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

    public function test_show_all_psfechaspago_successfully()
    {
        $mockModel = Mockery::mock(Psfechaspago::class);
        $mockModel->shouldReceive('where')->with('id_prestamo', 1)->andReturnSelf();
        $mockModel->shouldReceive('with')->andReturnSelf();
        $mockModel->shouldReceive('get')->andReturn(collect([]));

        $controller = Mockery::mock(PsfechaspagoController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('spanishDate')->andReturn('01 Enero 2024');

        $response = $controller->showAllPsfechaspago(1, $mockModel);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_all_psfechaspago_handles_exception()
    {
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('where')->andThrow(new \Exception('DB Error', 500));

        $controller = new PsfechaspagoController();
        $response = $controller->showAllPsfechaspago(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_show_one_psfechaspago_successfully()
    {
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        $controller = new PsfechaspagoController();
        $response = $controller->showOnePsfechaspago(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_psfechaspago_handles_exception()
    {
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('find')->andThrow(new \Exception('error', 500));

        $controller = new PsfechaspagoController();
        $response = $controller->showOnePsfechaspago(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_create_psfechaspago_successfully()
    {
        $request = new Request(['campo' => 'valor']);
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('create')->with($request->all())->andReturn((object)['id' => 1]);

        $controller = new PsfechaspagoController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_create_psfechaspago_handles_exception()
    {
        $request = new Request(['campo' => 'valor']);
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('error', 500));

        $controller = new PsfechaspagoController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_update_psfechaspago_successfully()
    {
        $request = new Request(['campo' => 'nuevo']);

        $model = Mockery::mock();
        $model->shouldReceive('update')->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsfechaspagoController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_update_psfechaspago_handles_exception()
    {
        $request = new Request();

        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Update error', 500));

        $controller = new PsfechaspagoController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_delete_psfechaspago_successfully()
    {
        $model = Mockery::mock();
        $model->shouldReceive('delete')->andReturn(true);

        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($model);

        $controller = new PsfechaspagoController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_delete_psfechaspago_handles_exception()
    {
        $mock = Mockery::mock(Psfechaspago::class);
        $mock->shouldReceive('findOrFail')->andThrow(new \Exception('Delete error', 500));

        $controller = new PsfechaspagoController();
        $response = $controller->delete(1, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }

    
}
