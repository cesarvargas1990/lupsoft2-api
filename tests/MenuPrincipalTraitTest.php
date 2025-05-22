<?php

namespace Tests\Unit;

use Illuminate\Http\JsonResponse;
use Mockery;

use Illuminate\Support\Facades\DB;
use App\Psusuperfil;
use TestCase;

class MenuPrincipalTraitTest extends TestCase
{
    public function test_perfil_accion_returns_correct_data()
    {
        // Simular el modelo Psusuperfil
        $mockPsusuperfil = Mockery::mock(Psusuperfil::class);
        $mockPsusuperfil->shouldReceive('where')->with('id_user', 1)->andReturnSelf();
        $mockPsusuperfil->shouldReceive('join')->with('psperfilaccion as p', 'psusperfil.id_perfil', '=', 'p.id_perfil')->andReturnSelf();
        $mockPsusuperfil->shouldReceive('select')->with('p.nom_accion')->andReturnSelf();
        $mockPsusuperfil->shouldReceive('get')->andReturn(collect([
            (object) ['nom_accion' => 'crear_usuario'],
            (object) ['nom_accion' => 'editar_usuario']
        ]));

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\menuPrincipalTrait;
        };

        // Llamar a la función con el mock
        $resultado = $classInstance->perfilAccion(1, $mockPsusuperfil);

        // Verificaciones
        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);
        $this->assertContains('crear_usuario', $resultado);
        $this->assertContains('editar_usuario', $resultado);
    }

    public function test_perfil_accion_handles_exception()
    {
        // Simular el modelo Psusuperfil lanzando una excepción
        $mockPsusuperfil = Mockery::mock(Psusuperfil::class);
        $mockPsusuperfil->shouldReceive('where')->with('id_user', 1)->andThrow(new \Exception('Error en la base de datos', 500));

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\menuPrincipalTrait;
        };

        // Capturar la respuesta cuando ocurre una excepción
        $resultado = $classInstance->perfilAccion(1, $mockPsusuperfil);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);
        $responseData = $resultado->getData(true);
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
    }

    public function test_hacer_menu_usuario_returns_correct_structure()
    {
        // Datos simulados de menú
        $datosMenu = [
            (object) ['id' => 1, 'nombre' => 'Dashboard', 'icono' => 'home', 'ruta' => '/dashboard', 'id_mpadre' => 0],
            (object) ['id' => 2, 'nombre' => 'Usuarios', 'icono' => 'users', 'ruta' => '/usuarios', 'id_mpadre' => 0],
            (object) ['id' => 3, 'nombre' => 'Configuraciones', 'icono' => 'settings', 'ruta' => '/config', 'id_mpadre' => 2]
        ];

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\menuPrincipalTrait;
        };

        // Llamar a la función con los datos simulados
        $resultado = $classInstance->hacerMenuUsuario($datosMenu);

        // Verificaciones
        $this->assertIsArray($resultado);
        $this->assertCount(2, $resultado);

        $this->assertEquals('Dashboard', $resultado[0]['displayName']);
        $this->assertEquals('Usuarios', $resultado[1]['displayName']);

        $this->assertArrayHasKey('children', $resultado[1]);
        $this->assertCount(1, $resultado[1]['children']);

        $this->assertEquals('Configuraciones', $resultado[1]['children'][0]['displayName']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
