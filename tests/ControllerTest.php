<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use App\PsEmpresa;
use App\Psusuperfil;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Laravel\Lumen\Testing\TestCase;

class ControllerTest extends TestCase
{
    /**
     * Inicializa la aplicación Lumen en la prueba.
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function test_respond_with_token_returns_correct_response()
    {
        // Simular un usuario autenticado
        $mockUser = Mockery::mock('User');
        $mockUser->id = 1;
        $mockUser->name = 'John Doe';
        $mockUser->email = 'johndoe@example.com';
        $mockUser->id_empresa = '1';
        $mockUser->is_admin = true;

        // Simular Auth::user()
        Auth::shouldReceive('user')->andReturn($mockUser);
        Auth::shouldReceive('factory')->andReturnSelf();
        Auth::shouldReceive('getTTL')->andReturn(60);

        // Simular el modelo PsEmpresa
        $mockPsempresa = Mockery::mock(PsEmpresa::class);
        $mockPsempresa->shouldReceive('where')->with('id_empresa', '1')->andReturnSelf();
        $mockPsempresa->shouldReceive('first')->andReturn((object)['id' => 1]);

        // Simular el modelo Psusuperfil
        $mockPsusuperfil = Mockery::mock(Psusuperfil::class);

        // Simular los métodos del trait menuPrincipalTrait
        $mockController = Mockery::mock(Controller::class)->makePartial();
        $mockController->shouldReceive('getDatosMenu')->with(1)->andReturn([(object)['id' => 1, 'nombre' => 'Dashboard', 'icono' => 'home', 'ruta' => '/dashboard', 'id_mpadre' => 0]]);
        $mockController->shouldReceive('hacerMenuUsuario')->andReturn([
            [
                'id' => 1,
                'displayName' => 'Dashboard',
                'iconName' => 'home',
                'route' => '/dashboard'
            ]
        ]);
        $mockController->shouldReceive('perfilAccion')->with(1, $mockPsusuperfil)->andReturn(['permiso1', 'permiso2']);

        // Simular ejecución de la función
        $token = 'test_token';
        $response = $mockController->respondWithToken($token, $mockPsempresa, $mockPsusuperfil);

        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);

        // Verificaciones
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, $responseData['id']);
        $this->assertEquals('John Doe', $responseData['name']);
        $this->assertEquals('johndoe@example.com', $responseData['email']);
        $this->assertEquals('test_token', $responseData['access_token']);
        $this->assertEquals('bearer', $responseData['token_type']);
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals([
            [
                'id' => 1,
                'displayName' => 'Dashboard',
                'iconName' => 'home',
                'route' => '/dashboard'
            ]
        ], $responseData['menu_usuario']);
        $this->assertEquals(['permiso1', 'permiso2'], $responseData['permisos']);
        $this->assertEquals(3600, $responseData['expires_in']);
        $this->assertIsInt($responseData['time']);
        $this->assertTrue($responseData['is_admin']);
        $this->assertEquals('1', $responseData['id_empresa']);
        $this->assertEquals(1, $responseData['id_empresa']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
