<?php

namespace Tests\Unit;

use App\Http\Controllers\GuardarArchivoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class GuardarArchivoControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

   

    public function test_guardar_archivo_adjunto_without_image()
    {
        $request = new Request();

        $controller = new GuardarArchivoController();
        $response = $controller->guardarArchivoAdjunto($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('error', json_decode($response->getContent(), true)['status']);
    }
    

    
    
}
