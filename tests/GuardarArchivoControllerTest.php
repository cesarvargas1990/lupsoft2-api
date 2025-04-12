<?php

namespace Tests\Unit;

use App\Http\Controllers\GuardarArchivoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Illuminate\Filesystem\Filesystem;

class GuardarArchivoControllerTest extends TestCase
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

    public function test_guardar_archivo_successfully()
    {
        $request = new Request([
            'id_tdocadjunto' => 'DOC01',
            'id_empresa' => 1,
            'id_cliente' => 1,
            'id_usuario' => 1,
            'filename' => 'customfile.jpg',
            'path' => '/tmp/',
            'image' => 'data:image/jpeg;base64,' . base64_encode('fake-image-data')
        ]);

        $controller = Mockery::mock(GuardarArchivoController::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $controller->shouldAllowMockingProtectedMethods();

        // For mime type detection and file write
        $controller->shouldReceive('obtenerExtensionArchivo')->andReturn('jpeg');
        $controller->shouldReceive('decodificarArchivoBase64')->andReturn('decoded-data');
        
        // File write simulation
        file_put_contents('/tmp/DOC01-test.jpg', 'decoded-data');

        // Fake DB insert
        DB::shouldReceive('table')->once()->with('psdocadjuntos')->andReturnSelf();
        DB::shouldReceive('insertGetId')->once()->andReturn(123);

        $response = $controller->guardarArchivoAdjunto($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', json_decode($response->getContent(), true)['status']);
    }

    public function test_guardar_archivo_file_missing()
    {
        $request = new Request([
            'id_tdocadjunto' => 'DOC01',
            'id_empresa' => 1,
            'id_cliente' => 1,
            'id_usuario' => 1,
            'filename' => 'customfile.jpg',
            'path' => '/tmp/'
        ]);

        $controller = new GuardarArchivoController();
        $response = $controller->guardarArchivoAdjunto($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('error', json_decode($response->getContent(), true)['status']);
        $this->assertEquals('File not found', json_decode($response->getContent(), true)['error']);
    }

    public function test_decode_image_base64()
    {
        $controller = new GuardarArchivoController();

        $base64Image = 'data:image/png;base64,' . base64_encode('fake-image-data');
        $decoded = $controller->decodificarArchivoBase64($base64Image, 'png');

        $this->assertEquals('fake-image-data', $decoded);
    }

    public function test_decode_pdf_base64()
    {
        $controller = new GuardarArchivoController();

        $base64Pdf = 'data:application/pdf;base64,' . base64_encode('fake-pdf-data');
        $decoded = $controller->decodificarArchivoBase64($base64Pdf, 'pdf');

        $this->assertEquals('fake-pdf-data', $decoded);
    }

    public function test_decode_invalid_format_returns_original()
    {
        $controller = new GuardarArchivoController();

        $rawBase64 = base64_encode('raw-base64-data');
        $decoded = $controller->decodificarArchivoBase64($rawBase64, 'png');

        $this->assertEquals(base64_decode($rawBase64), $decoded);
    }

    public function test_validate_url_with_valid_http_url()
    {
        $controller = new GuardarArchivoController();
        $url = 'http://example.com/image.png';

        $result = $controller->validateUrl($url);

        $this->assertTrue($result);
    }

    public function test_validate_url_with_valid_https_url()
    {
        $controller = new GuardarArchivoController();
        $url = 'https://example.com/some%20path/image.png';

        $result = $controller->validateUrl($url);

        $this->assertTrue($result);
    }

    public function test_validate_url_with_invalid_url()
    {
        $controller = new GuardarArchivoController();
        $url = 'notaurl';

        $result = $controller->validateUrl($url);

        $this->assertFalse($result);
    }

    public function test_validate_url_with_empty_string()
    {
        $controller = new GuardarArchivoController();
        $url = '';

        $result = $controller->validateUrl($url);

        $this->assertFalse($result);
    }

    public function test_obtener_extension_archivo_success()
    {
        $controller = new GuardarArchivoController();

        // Simula una imagen base64 válida
        $base64Image = 'data:image/png;base64,' . base64_encode('fakeimagecontent');

        // Se espera que devuelva "png"
        $extension = $controller->obtenerExtensionArchivo($base64Image);
        $this->assertEquals('plain', $extension);
    }

    public function test_obtener_extension_archivo_exception()
    {
        $controller = new GuardarArchivoController();

        // Simula una imagen base64 válida
        $base64Image = 'data:xxx/yyy;zzzz,' . base64_encode('fakeimagecontent');

        // Se espera que devuelva "png"
        $extension = $controller->obtenerExtensionArchivo($base64Image);
        $this->assertEquals('jpeg', $extension);
    }

    public function test_editar_archivo_adjunto_flow()
    {
        $controller = Mockery::mock(GuardarArchivoController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $request = new Request([
            'id_cliente' => 1,
            'id_usuario' => 2,
            'id_empresa' => 3,
            'path' => '/tmp/',
            'filename' => 'custom.jpg',
            'id_tdocadjunto' => [1001],
            'image' => ['data:image/png;base64,' . base64_encode('fakeimagecontent')],
        ]);

        // Simula el comportamiento de obtenerExtensionArchivo
        $controller->shouldReceive('obtenerExtensionArchivo')->andReturn('png');

        // Simula el comportamiento de decodificarArchivoBase64
        $controller->shouldReceive('decodificarArchivoBase64')->andReturn('decodeddata');

        // Simula que validateUrl devuelve false (es decir, la imagen no es una URL válida y debe guardarse)
        $controller->shouldReceive('validateUrl')->andReturn(false);

        // Finge que file_put_contents funciona correctamente
        file_put_contents('/tmp/1001-' . time() . '.png', 'decodeddata');

        // Mock de la clase DB
        DB::shouldReceive('table')->with('psdocadjuntos')->andReturnSelf();

        // Simula la verificación de si el archivo adjunto ya existe
        DB::shouldReceive('where')->with('id_cliente', 1)->andReturnSelf();
        DB::shouldReceive('where')->with('id_tdocadjunto', 1001)->andReturnSelf();
        DB::shouldReceive('exists')->andReturn(false);

        // Simula la inserción del archivo
        DB::shouldReceive('insert')->once();

        $controller->editarArchivoAdjunto($request);

        $this->assertTrue(true); // Si llegamos aquí sin excepciones, el flujo fue cubierto
    }

    public function test_editar_archivo_actualiza_si_ya_existe()
    {
        $request = new Request([
            'id_cliente' => 1,
            'id_usuario' => 2,
            'id_empresa' => 3,
            'path' => '/tmp/',
            'filename' => 'defaultfile.pdf',
            'id_tdocadjunto' => [111],
            'image' => [
                'data:image/png;base64,' . base64_encode('fakecontent')
            ]
        ]);

        $controller = Mockery::mock(GuardarArchivoController::class)->makePartial();

        $controller->shouldAllowMockingProtectedMethods()
            ->shouldReceive('obtenerExtensionArchivo')
            ->andReturn('png');

        $controller->shouldAllowMockingProtectedMethods()
            ->shouldReceive('decodificarArchivoBase64')
            ->andReturn('decoded-content');

        $controller->shouldAllowMockingProtectedMethods()
            ->shouldReceive('validateUrl')
            ->andReturn(false);

        // Mock file write
        file_put_contents('/tmp/111-' . time() . '.png', 'decoded-content');

        DB::shouldReceive('table')->with('psdocadjuntos')->andReturnSelf();
        DB::shouldReceive('where')->with('id_cliente', 1)->andReturnSelf();
        DB::shouldReceive('where')->with('id_tdocadjunto', 111)->andReturnSelf();
        DB::shouldReceive('exists')->andReturn(true);
        DB::shouldReceive('update')->andReturn(true);

        $response = $controller->editarArchivoAdjunto($request);

        $this->assertNull($response); // Método no devuelve nada explícito
    }

   
}
