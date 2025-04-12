<?php

namespace Tests\Unit;

use App\Http\Controllers\PrestamosController;
use App\Pspagos;
use App\Psperiodopago;
use App\Psprestamos;
use App\Pspstiposistemaprest;
use App\Pstdocplant;
use App\Psquerytabla;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\PsEmpresa;
class PrestamosControllerTest extends TestCase
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

    public function test_guardar_prestamo_successfully()
    {
        $request = new Request([
            'id_cliente' => 1,
            'valorpres' => 100000,
            'numcuotas' => 12
        ]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('guardarPrestamoFechas')
            ->once()
            ->with($request, Mockery::type(Psperiodopago::class), Mockery::type(Pspstiposistemaprest::class))
            ->andReturn(['status' => 'ok', 'message' => 'Préstamo guardado']);

        $response = $controller->guardarPrestamo($request, new Psperiodopago(), new Pspstiposistemaprest());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Préstamo guardado', json_decode($response->getContent(), true)['message']);
    }

    public function test_guardar_prestamo_exception()
    {
        $mockRequest = new Request();
        $periodo = Mockery::mock(Psperiodopago::class);
        $sistema = Mockery::mock(Pspstiposistemaprest::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldReceive('guardarPrestamoFechas')->andThrow(new \Exception('Error guardando'));

        $response = $controller->guardarPrestamo($mockRequest, $periodo, $sistema);
        $this->assertEquals(404, $response->getStatusCode());
    }


    public function test_listado_prestamos_successfully()
    {
        $mockData = [
            ["id" => 1, "cliente" => "Juan Perez"],
            ["id" => 2, "cliente" => "Maria Lopez"]
        ];

        $request = new Request(['id_empresa' => 5]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldReceive('consultaListadoPrestamos')
            ->with(5)
            ->once()
            ->andReturn($mockData);

        $response = $controller->listadoPrestamos($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($mockData, $response->getData(true));
    }


    public function test_listado_prestamos_exception()
    {
        $request = new Request(['id_empresa' => 1]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldReceive('consultaListadoPrestamos')->andThrow(new \Exception('Error listando'));

        $response = $controller->listadoPrestamos($request);
        $this->assertEquals(404, $response->getStatusCode());
    }

   

    public function test_get_plantillas_documentos_exception()
    {
        $request = new Request();
        $queryTabla = Mockery::mock(Psquerytabla::class);
        $docplant = Mockery::mock(Pstdocplant::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldReceive('renderTemplate')->andThrow(new \Exception('Error renderizando'));

        $response = $controller->getPlantillasDocumentosPrestamo($request, $queryTabla, $docplant);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_total_prestado_exception()
    {
        Auth::shouldReceive('user')->andReturn((object)['perfiles' => collect([(object)['id' => 2]])]);
        $controller = new PrestamosController();
        $response = $controller->totalprestado(1);
        $this->assertEquals('NA', $response);
    }

    public function test_total_interes_exception()
    {
        $request = new Request();
        $pagos = Mockery::mock(Pspagos::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldReceive('getTotalintereses')->andThrow(new \Exception('Error intereses'));

        $response = $controller->totalinteres($request, $pagos);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_generar_variables_plantillas_returns_data()
    {
        $mockData = [(object)[
            'id' => 1,
            'nombre' => 'Juan',
            'valor' => 1000
        ]];

        DB::shouldReceive('select')
            ->once()
            ->andReturn($mockData);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                   ->shouldReceive('obtenerQryListadoPrestamos')
                   ->with(1)
                   ->andReturn('SELECT * FROM prestamos WHERE id_empresa = :id_empresa');

        $result = $controller->generarVariablesPlantillas(1);

        $this->assertIsArray($result);
        $this->assertEquals('id', $result[0]['title']);
        $this->assertEquals('{id}', $result[0]['content']);
    }

    public function test_generar_variables_plantillas_returns_null_when_no_data()
    {
        DB::shouldReceive('select')
            ->once()
            ->andReturn([]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                   ->shouldReceive('obtenerQryListadoPrestamos')
                   ->with(1)
                   ->andReturn('SELECT * FROM prestamos WHERE id_empresa = :id_empresa');

        $result = $controller->generarVariablesPlantillas(1);

        $this->assertNull($result);
    }

    public function test_get_plantillas_documentos_prestamo_returns_successful_response()
    {
        $request = new Request(['id_empresa' => 1]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                   ->shouldReceive('renderTemplate')
                   ->with($request, Mockery::type(Psquerytabla::class), Mockery::type(Pstdocplant::class))
                   ->once()
                   ->andReturn([
                       'documento' => 'Plantilla generada exitosamente'
                   ]);

        $response = $controller->getPlantillasDocumentosPrestamo($request, new Psquerytabla(), new Pstdocplant());

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('documento', $content);
    }

    public function test_get_plantillas_documentos_prestamo_handles_exception()
    {
        $request = new Request(['id_empresa' => 1]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                   ->shouldReceive('renderTemplate')
                   ->andThrow(new \Exception('Error generando plantilla', 500));

        $response = $controller->getPlantillasDocumentosPrestamo($request, new Psquerytabla(), new Pstdocplant());

        $this->assertEquals(404, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Error generando plantilla', $content['message']);
        $this->assertEquals(500, $content['errorCode']);
    }

    public function test_eliminar_prestamo_marks_as_inactive()
    {
        DB::shouldReceive('table')->with('psprestamos')->once()->andReturnSelf();
        DB::shouldReceive('where')->with('id', '=', 5)->once()->andReturnSelf();
        DB::shouldReceive('update')->with(['ind_estado' => 0])->once();

        DB::shouldReceive('table')->with('psfechaspago')->once()->andReturnSelf();
        DB::shouldReceive('where')->with('id_prestamo', '=', 5)->once()->andReturnSelf();
        DB::shouldReceive('update')->with(['ind_estado' => 0])->once();

        DB::shouldReceive('table')->with('pspagos')->once()->andReturnSelf();
        DB::shouldReceive('where')->with('id_prestamo', '=', 5)->once()->andReturnSelf();
        DB::shouldReceive('update')->with(['ind_estado' => 0])->once();

        $controller = new PrestamosController();
        $controller->eliminarPrestamo(5);

        $this->assertTrue(true); // Solo verificamos que no haya errores
    }

    public function test_eliminar_prestamo_with_empty_id_does_nothing()
    {
        DB::shouldReceive('table')->never();

        $controller = new PrestamosController();
        $controller->eliminarPrestamo("");

        $this->assertTrue(true);
    }

    public function test_totalprestadohoy_returns_na_when_user_has_no_profile()
    {
        $request = new Request();

        //$mockAuth = Mockery::mock('alias:Illuminate\\Support\\Facades\\Auth');
        Auth::shouldReceive('user')->andReturn((object) [
            'perfiles' => collect([['id' => 2]]) // no tiene perfil id=1
        ]);

        $controller = new PrestamosController();
        $response = $controller->totalprestadohoy($request, Mockery::mock(Psprestamos::class));

        $this->assertEquals('NA', $response);
    }

    public function test_totalprestadohoy_returns_total()
    {
        $request = new Request();

        //$mockAuth = Mockery::mock('alias:Illuminate\\Support\\Facades\\Auth');
        Auth::shouldReceive('user')->andReturn((object) [
            'perfiles' => collect([(object)['id' => 1]])
        ]);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                  ->shouldReceive('getTotalPrestadoHoy')
                  ->once()
                  ->with($request, Mockery::type(Psprestamos::class))
                  ->andReturn(500000);

        $response = $controller->totalprestadohoy($request, new Psprestamos());

        $this->assertEquals('500,000.00', $response);
    }

    public function test_totalprestadohoy_handles_exception()
    {
        $request = new Request();

        
        Auth::shouldReceive('user')->andThrow(new \Exception('Fallo de autenticación', 401));

        $controller = new PrestamosController();
        $response = $controller->totalprestadohoy($request, new Psprestamos());

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Fallo de autenticación', json_decode($response->getContent(), true)['message']);
    }

    public function test_totalprestado_handles_exception()
    {
        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();

        // Simula que Auth::user() lanza una excepción
        Auth::shouldReceive('user')->andThrow(new \Exception('Auth failed', 500));

        $response = $controller->totalprestado(1);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Auth failed', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }

    public function test_totalintereshoy_returns_formatted_total()
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockPagos = Mockery::mock(Pspagos::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('getTotalintereseshoy')
            ->with($mockRequest, $mockPagos)
            ->once()
            ->andReturn(12345.678);

        $response = $controller->totalintereshoy($mockRequest, $mockPagos);

        $this->assertEquals('12,345.68', $response);
    }

    public function test_totalintereshoy_handles_exception()
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockPagos = Mockery::mock(Pspagos::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('getTotalintereseshoy')
            ->with($mockRequest, $mockPagos)
            ->andThrow(new \Exception('Fallo al calcular', 500));

        $response = $controller->totalintereshoy($mockRequest, $mockPagos);

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Fallo al calcular', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }

    public function test_totalcapital_returns_na_when_user_has_no_profile()
    {
        $request = new Request();

        // Simular Auth::user()->perfiles->contains('id', 1) => false
        $userMock = Mockery::mock();
        $userMock->perfiles = collect();
        Auth::shouldReceive('user')->once()->andReturn($userMock);

        $controller = new PrestamosController();
        $response = $controller->totalcapital(1, $request, new PsEmpresa(), new Psprestamos(), new Pspagos(), new Auth());

        $this->assertEquals('NA', $response);
    }

    public function test_totalcapital_returns_correct_value()
    {
        $request = new Request();

        $empresaMock = Mockery::mock(PsEmpresa::class);
        $prestamosMock = Mockery::mock(Psprestamos::class);
        $pagosMock = Mockery::mock(Pspagos::class);

        $controller = Mockery::mock(PrestamosController::class)->makePartial();

        $controller->shouldAllowMockingProtectedMethods()
                  ->shouldReceive('getCapitalInicial')->with(1, $empresaMock)->andReturn(5000);

        $controller->shouldReceive('getValorPrestamos')->with($request, $prestamosMock)->andReturn(2000);
        $controller->shouldReceive('getTotalintereses')->with($request, $pagosMock, Mockery::any())->andReturn(500);

        $userMock = Mockery::mock();
        $userMock->perfiles = collect([(object)['id' => 1]]);
        Auth::shouldReceive('user')->once()->andReturn($userMock);

        $response = $controller->totalcapital(1, $request, $empresaMock, $prestamosMock, $pagosMock, new Auth());

        $this->assertEquals('3,500.00', $response);
    }

    public function test_totalcapital_handles_exception()
    {
        $request = new Request();

        $controller = Mockery::mock(PrestamosController::class)->makePartial();
        $controller->shouldAllowMockingProtectedMethods()
                  ->shouldReceive('getCapitalInicial')->andThrow(new \Exception('Error de prueba', 500));

        $userMock = Mockery::mock();
        $userMock->perfiles = collect([(object)['id' => 1]]);
        Auth::shouldReceive('user')->andReturn($userMock);

        $response = $controller->totalcapital(1, $request, new PsEmpresa(), new Psprestamos(), new Pspagos(), new Auth());

        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Error de prueba', $data['message']);
        $this->assertEquals(500, $data['errorCode']);
    }

    

    
}
