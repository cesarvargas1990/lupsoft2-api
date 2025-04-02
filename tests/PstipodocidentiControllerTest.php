<?php

namespace Tests\Unit;

use App\Http\Controllers\PstipodocidentiController;
use App\Pstipodocidenti;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Illuminate\Support\Facades\DB;

class PstipodocidentiControllerTest extends TestCase
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

    public function test_show_all_pstipodocidenti_returns_all_data()
    {
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('all')->once()->andReturn([['id' => 1]]);

        $controller = new PstipodocidentiController();
        $response = $controller->showAllPstipodocidenti($mock);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_show_one_pstipodocidenti_returns_correct_data()
    {
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('find')->with(1)->andReturn((object)['id' => 1]);

        $controller = new PstipodocidentiController();
        $response = $controller->showOnePstipodocidenti($mock, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['id' => 1], json_decode($response->getContent(), true));
    }

    public function test_show_select_data_successfully()
    {
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('select')->with('codtipdocid as value', 'nomtipodocumento as label')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn([
            ['value' => 'CC', 'label' => 'Cédula']
        ]);

        $controller = new PstipodocidentiController();
        $response = $controller->ShowPstipodocidenti($mock);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('CC', $data[0]['value']);
        $this->assertEquals('Cédula', $data[0]['label']);
    }


    public function test_show_select_data_handles_exception()
    {
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('select')
            ->with(['codtipdocid as value', 'nomtipodocumento as label'])
            ->andThrow(new \Exception('Fallo al obtener datos', 500));

        $controller = new PstipodocidentiController();
        $response = $controller->ShowPstipodocidenti($mock);

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        //$this->assertEquals('Fallo al obtener datos', $data['message']);
        //$this->assertEquals(500, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_create_pstipodocidenti_successfully()
    {
        $request = new Request(['codtipdocid' => 'TI', 'nomtipodocumento' => 'Tarjeta Identidad']);

        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('create')->once()->with($request->all())->andReturn((object)['id' => 1]);

        $controller = new PstipodocidentiController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_update_pstipodocidenti_successfully()
    {
        $request = new Request(['nomtipodocumento' => 'Pasaporte']);

        $mockEntity = Mockery::mock();
        $mockEntity->shouldReceive('update')->once()->with($request->all())->andReturn(true);

        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('findOrFail')->with(1)->andReturn($mockEntity);

        $controller = new PstipodocidentiController();
        $response = $controller->update(1, $request, $mock);

        $this->assertEquals(200, $response->getStatusCode());
    }



    public function test_show_one_pstipodocidenti_handles_exception()
    {
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('find')
            ->with(999)
            ->andThrow(new \Exception('Error inesperado', 500));

        $controller = new PstipodocidentiController();
        $response = $controller->showOnePstipodocidenti($mock, 999);

        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Error inesperado', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
        $this->assertArrayHasKey('lineError', $data);
        $this->assertArrayHasKey('file', $data);
    }

    public function test_create_pstipodocidenti_handles_exception()
    {
        $request = new Request(['codtipdocid' => 'XX']);
        $mock = Mockery::mock(Pstipodocidenti::class);
        $mock->shouldReceive('create')->andThrow(new \Exception('Error creando', 500));

        $controller = new PstipodocidentiController();
        $response = $controller->create($request, $mock);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
