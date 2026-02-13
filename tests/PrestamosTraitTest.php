<?php

namespace Tests\Unit;

use App\Http\Traits\General\prestamosTrait;
use App\PsEmpresa;
use App\Pspagos;
use App\Psperiodopago;
use App\Psprestamos;
use App\Pspstiposistemaprest;
use App\Psquerytabla;
use App\Pstdocplant;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery;
use TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrestamosTraitTestDummy
{
    use \App\Http\Traits\General\prestamosTrait;

    /**
     * Este método se burla (mock) en el test,
     * para simular la respuesta de calcularCuota().
     */
    public function calcularCuota($request)
    {
        // Implementación real si existiera, aquí queda vacío.
    }
}

class PrestamosTraitTest extends TestCase
{
    /**
     * Prueba unitaria de guardarPrestamoFechas().
     */
    public function test_get_datos_menu_returns_correct_data()
    {
        // Simular la respuesta de DB::select
        DB::shouldReceive('select')
            ->once()
            ->with(Mockery::type('string'), Mockery::subset(['id' => 1]))
            ->andReturn([
                (object) ['id' => 1, 'nombre' => 'Dashboard', 'icono' => 'home', 'ruta' => '/dashboard', 'id_mpadre' => 0],
                (object) ['id' => 2, 'nombre' => 'Usuarios', 'icono' => 'users', 'ruta' => '/usuarios', 'id_mpadre' => 0],
                (object) ['id' => 3, 'nombre' => 'Configuraciones', 'icono' => 'settings', 'ruta' => '/config', 'id_mpadre' => 2]
            ]);

        // Crear una instancia de la clase que contiene la función con el trait
        $classInstance = new class {
            use \App\Http\Traits\General\menuPrincipalTrait;
        };

        // Llamar a la función con el ID del usuario
        $resultado = $classInstance->getDatosMenu(1);

        // Verificaciones
        $this->assertIsArray($resultado);
        $this->assertCount(3, $resultado);
        $this->assertEquals('Dashboard', $resultado[0]->nombre);
        $this->assertEquals('Usuarios', $resultado[1]->nombre);
        $this->assertEquals('Configuraciones', $resultado[2]->nombre);
    }

    public function testConsultaListadoPrestamos()
    {
        // 1. Define un nit_empresa de prueba
        $id_empresa = '123456789';

        // 2. Crea un mock parcial de la clase dummy
        //    que internamente usa el trait.
        $dummy = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // 3. Simula la respuesta de obtenerQryListadoPrestamos($id_empresa)
        //    Devolvemos un query ficticio.
        $dummy->shouldReceive('obtenerQryListadoPrestamos')
            ->once()
            ->andReturn('SELECT * FROM psprestamos WHERE id_empresa = :id_empresa');

        // 4. Preparamos un array de objetos que simulará la respuesta de DB::select
        $mockData = [
            (object) ['id' => 1, 'valorpres' => 1000],
            (object) ['id' => 2, 'valorpres' => 2000],
        ];

        // 5. Hacemos mock de DB::select
        DB::shouldReceive('select')
            ->once()
            ->with(
                'SELECT * FROM psprestamos WHERE id_empresa = :id_empresa',
                ['id_empresa' => $id_empresa]
            )
            ->andReturn($mockData);

        // 6. Invocamos el método a probar
        $result = $dummy->consultaListadoPrestamos($id_empresa);

        // 7. Verificamos que se devuelva el mismo array que simulamos
        $this->assertEquals($mockData, $result, 'Debe retornar los resultados de DB::select');
    }

    public function testObtenerQryListadoPrestamos()
    {
        // 1. Prepara el nit_empresa de ejemplo
        $id_empresa = '123456789';

        // 2. Instancia la clase dummy que usa el trait (no hace falta mock porque este método no llama a DB)
        $dummy = new PrestamosTraitTestDummy();

        // 3. Ejecuta el método
        $result = $dummy->obtenerQryListadoPrestamos();

        // 4. Construimos el string que esperamos
        //    (debe coincidir exactamente con lo que retorna el método)
        $expectedQuery = "
        SELECT date_format(CURDATE(),'%d/%m/%Y') fecha_actual,
        date_format(CURRENT_TIME(), '%H:%i:%s %p') hora_actua,
        pre.id id_prestamo,
        format(pre.valorpres,2) valorpresf,
        (
            SELECT da.rutaadjunto
            FROM psdocadjuntos da
            WHERE da.id_empresa = pre.id_empresa
            AND da.id_cliente = pre.id_cliente
            AND da.id_tdocadjunto = 3
            ORDER BY da.id DESC
            LIMIT 1
        ) firma_cliente,
        pre.*,
        cli.*,
        em.*,
        ide.*,
        tsip.*,
        pp.*,
        pp.nomperiodopago nomfpago
        FROM psprestamos pre,
        psclientes cli,
        psempresa em,
        pstipodocidenti ide,
        pstiposistemaprest tsip,
        psperiodopago pp
        WHERE pre.id_empresa = :id_empresa
        AND pre.id_cliente = cli.id
        and pre.id_tipo_sistema_prest  = tsip.id
        and pp.id = pre.id_periodo_pago
        AND em.id = pre.id_empresa
        AND  cli.id_tipo_docid = ide.id
        AND pre.ind_estado = 1";

        // 5. Afirmamos que el resultado sea igual al string esperado
        $this->assertEquals($expectedQuery, $result);
    }

    public function testConsultaVariablesPrestamo()
    {
        // 1. Crea un mock parcial de la clase dummy que usa el trait
        $dummy = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // 2. Define valores de ejemplo
        $id_empresa = '1';
        $id_prestamo = 10;

        // 3. Simula la respuesta de obtenerQryListadoPrestamos($id_empresa)
        //    Devuelve la parte base del query; luego se espera que el método
        //    añada " and pre.id = :id_prestamo"
        $baseQuery = 'SELECT * FROM psprestamos pre WHERE pre.id_empresa = :id_empresa AND pre.ind_estado = 1';
        $dummy->shouldReceive('obtenerQryListadoPrestamos')
            ->once()
            ->andReturn($baseQuery);

        // 4. Prepara el query final que esperamos
        $expectedQuery = $baseQuery . ' and pre.id = :id_prestamo';

        // 5. Simulamos datos de ejemplo que DB::select retornará
        $mockData = [
            (object) ['id' => 10, 'valorpres' => 15000],
            (object) ['id' => 11, 'valorpres' => 20000],
        ];

        // 6. Mock de DB::select con la consulta y los binds esperados
        DB::shouldReceive('select')
            ->once()
            ->with($expectedQuery, [
                'id_empresa' => $id_empresa,
                'id_prestamo' => $id_prestamo
            ])
            ->andReturn($mockData);

        // 7. Ejecuta el método a probar
        $result = $dummy->consultaVariablesPrestamo($id_empresa, $id_prestamo);

        // 8. Verifica que retorne exactamente el array simulado
        $this->assertEquals($mockData, $result, 'Debe retornar el resultado de DB::select');
    }

    public function testReplaceVariablesInTemplate()
    {
        // 1. Instancia de la clase dummy que usa el trait
        $dummy = new PrestamosTraitTestDummy();

        // 2. Preparamos un template con llaves que incluyan y no incluyan un símbolo $
        $template = "Hola {nombre}, tu saldo es {dinero$}, y tu fecha de corte es {fecha}.";

        // 3. Definimos el array de variables que se usarán para reemplazar
        $variables = [
            'nombre' => 'Carlos',
            'dinero' => 1500,
            'fecha' => '2023-03-01'
        ];

        // 4. Llamamos a la función a testear
        $resultado = $dummy->replaceVariablesInTemplate($template, $variables);

        // 5. Verificamos el resultado esperado:
        //    - {nombre} => "Carlos"
        //    - {dinero$} => "1500" (se quita el '$' final para la clave "dinero")
        //    - {fecha} => "2023-03-01"
        $this->assertEquals(
            "Hola Carlos, tu saldo es 1500, y tu fecha de corte es 2023-03-01.",
            $resultado
        );
    }


    public function test_get_capital_prestado_returns_correct_value()
    {
        // Crear un mock de Psprestamos
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        // Configurar el mock para que permita llamadas encadenadas
        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf(); // Retorna el mismo mock para permitir encadenamiento

        $mockPsprestamos->shouldReceive('sum')
            ->with('valorpres')
            ->andReturn(50000.00); // Simula que devuelve 50000

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Llamar a la función con el mock de Psprestamos
        $resultado = $traitInstance->getCapitalPrestado(123456, $mockPsprestamos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(50000.00, $resultado);
    }

    public function test_get_capital_prestado_handles_exception()
    {
        // Crear un mock de Psprestamos que lance una excepción
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andThrow(new \Exception('Error en la base de datos', 123));

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Llamar a la función con el mock de Psprestamos
        $resultado = $traitInstance->getCapitalPrestado(123456, $mockPsprestamos);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);

        // Decodificar el contenido de la respuesta JSON
        $responseData = $resultado->getData(true);

        // Verificar que el mensaje de error y el código sean los esperados
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(123, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    public function test_get_capital_inicial_returns_correct_value()
    {
        // Crear un mock de Psempresa
        $mockPsempresa = Mockery::mock(Psempresa::class);

        // Configurar el mock para que permita llamadas encadenadas
        $mockPsempresa->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();

        $mockPsempresa->shouldReceive('value')
            ->with('vlr_capinicial')
            ->andReturn(75000.00); // Simula que devuelve 75000

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con el mock de Psempresa
        $resultado = $traitInstance->getCapitalInicial(123456, $mockPsempresa);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(75000.00, $resultado);
    }

    public function test_get_capital_inicial_handles_exception()
    {
        // Crear un mock de Psempresa que lance una excepción
        $mockPsempresa = Mockery::mock(PsEmpresa::class);

        $mockPsempresa->shouldReceive('where')
            ->withAnyArgs()
            ->andThrow(new \Exception('Error en la base de datos', 500));

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con el mock de Psempresa
        $resultado = $traitInstance->getCapitalInicial(123456, $mockPsempresa);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);

        // Decodificar el contenido de la respuesta JSON
        $responseData = $resultado->getData(true);

        // Verificar que el mensaje de error y el código sean los esperados
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    public function test_get_total_capital_returns_correct_value()
    {
        // Crear mocks de Psempresa y Psprestamos
        $mockPsempresa = Mockery::mock(Psempresa::class);
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        // Configurar el mock de Psempresa
        $mockPsempresa->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsempresa->shouldReceive('value')
            ->with('vlr_capinicial')
            ->andReturn(75000.00);

        // Configurar el mock de Psprestamos
        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('sum')
            ->with('valorpres')
            ->andReturn(50000.00);

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $traitInstance->getTotalCapital(123456, $mockPsempresa, $mockPsprestamos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(25000.00, $resultado);
    }

    public function test_get_total_prestado_hoy_returns_correct_value()
    {
        // Crear un mock de Psprestamos
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        // Configurar el mock para simular la consulta
        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('whereBetween')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('sum')
            ->with('valorpres')
            ->andReturn(15000.00);

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Simular el request
        $request = new Request([
            'id_empresa' => 123456,
            'fecha' => '2025-03-04'
        ]);

        // Llamar a la función con el mock
        $resultado = $traitInstance->getTotalPrestadoHoy($request, $mockPsprestamos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(15000.00, $resultado);
    }

    public function test_get_total_prestado_hoy_handles_exception()
    {
        // Crear un mock de Psprestamos que lance una excepción
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('whereBetween')
            ->withAnyArgs()
            ->andThrow(new \Exception('Error en la base de datos', 500));

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Simular el request
        $request = new Request([
            'id_empresa' => 123456,
            'fecha' => '2025-03-04'
        ]);

        // Llamar a la función con el mock
        $resultado = $traitInstance->getTotalPrestadoHoy($request, $mockPsprestamos);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);

        // Decodificar el contenido de la respuesta JSON
        $responseData = $resultado->getData(true);

        // Verificar que el mensaje de error y el código sean los esperados
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    public function test_get_valor_prestamos_returns_correct_value()
    {
        // Crear mock de Psprestamos
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        // Configurar el mock para simular la consulta
        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('sum')
            ->with('valorpres')
            ->andReturn(50000.00);

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Simular el request
        $request = new Request([
            'id_empresa' => 123456
        ]);

        // Llamar a la función con el mock
        $resultado = $traitInstance->getValorPrestamos($request, $mockPsprestamos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(50000.00, $resultado);
    }

    public function test_get_valor_prestamos_handles_exception()
    {
        // Crear mock de Psprestamos que lance una excepción
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        $mockPsprestamos->shouldReceive('where')
            ->withAnyArgs()
            ->andReturnSelf();
        $mockPsprestamos->shouldReceive('sum')
            ->with('valorpres')
            ->andThrow(new \Exception('Error en la base de datos', 500));

        // Crear una instancia de una clase que use el trait
        $traitInstance = new class {
            use PrestamosTrait;
        };

        // Simular el request
        $request = new Request([
            'id_empresa' => 123456
        ]);

        // Llamar a la función con el mock
        $resultado = $traitInstance->getValorPrestamos($request, $mockPsprestamos);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);

        // Decodificar el contenido de la respuesta JSON
        $responseData = $resultado->getData(true);

        // Verificar que el mensaje de error y el código sean los esperados
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }

    public function test_get_perfil_user_returns_correct_id()
    {
        // Crear el usuario mock con perfiles simulados
        $mockUser = Mockery::mock(User::class)->makePartial();

        // Crear un mock de la relación BelongsToMany
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 1]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 1]]));

        // Simular la relación perfiles correctamente
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        // Simular la autenticación
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función sin pasar Auth como parámetro
        $resultado = $classInstance->getPerfilUser();

        // Verificaciones
        $this->assertEquals(1, $resultado);
    }

    public function test_get_total_intereses_returns_correct_value3()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1'
        ]);

        // Crear el usuario mock con perfiles simulados
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Crear un mock de la relación BelongsToMany
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 1]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 1]]));

        // Simular la relación perfiles correctamente
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        // Simular la autenticación
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(20000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereses($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(20000.00, $resultado);
    }

    public function test_get_total_intereses_returns_correct_value2()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1'
        ]);

        // Crear el usuario mock con perfiles simulados
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Crear un mock de la relación BelongsToMany
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 2]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 2]]));

        // Simular la relación perfiles correctamente
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        // Simular la autenticación
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(20000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereses($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(20000.00, $resultado);
    }

    public function test_get_total_intereses3_returns_correct_value()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1'
        ]);

        // Crear el usuario mock con perfiles simulados
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Crear un mock de la relación BelongsToMany
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 1]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 1]]));

        // Simular la relación perfiles correctamente
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        // Simular la autenticación
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(20000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereses($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(20000.00, $resultado);
    }

    public function test_get_total_intereses_hoy_returns_correct_value()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1',
            'fecha' => '2025-03-06'
        ]);

        // Simular el usuario autenticado
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Simular la relación perfiles
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 1]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 1]]));
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('whereBetween')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(15000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereseshoy($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(15000.00, $resultado);
    }

    public function test_get_total_intereses_returns_correct_value()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1'
        ]);

        // Crear el usuario mock con perfiles simulados
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Crear un mock de la relación BelongsToMany
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn((object) ['id' => 1]);
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([(object) ['id' => 1]]));

        // Simular la relación perfiles correctamente
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        // Simular la autenticación
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(20000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereses($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(20000.00, $resultado);
    }

    public function test_get_total_intereses_hoy_returns_correct_value_for_specific_user()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1',
            'fecha' => '2025-03-06'
        ]);

        // Simular el usuario autenticado sin perfil de admin
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(2); // Usuario sin perfil admin

        // Simular la relación perfiles
        $mockRelacion = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');
        $mockRelacion->shouldReceive('firstWhere')->with('id', 1)->andReturn(null); // No tiene perfil admin
        $mockRelacion->shouldReceive('getResults')->andReturn(collect([]));
        $mockUser->shouldReceive('perfiles')->andReturn($mockRelacion);

        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('whereBetween')->withAnyArgs()->andReturnSelf();
        $mockPspagos->shouldReceive('where')->with('id_usureg', 2)->andReturnSelf();
        $mockPspagos->shouldReceive('sum')->with('valcuota')->andReturn(8000.00);

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con los mocks
        $resultado = $classInstance->getTotalintereseshoy($request, $mockPspagos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(8000.00, $resultado);
    }

    public function test_get_total_intereses_handles_exception()
    {
        // Simular el request
        $request = new Request([
            'id_empresa' => '1'
        ]);

        // Simular la autenticación
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('id')->andReturn(1);
        Auth::shouldReceive('user')->andReturn($mockUser);

        // Simular el modelo Pspagos que lanza una excepción
        $mockPspagos = Mockery::mock(Pspagos::class);
        $mockPspagos->shouldReceive('where')->withAnyArgs()->andThrow(new \Exception('Error en la base de datos', 500));

        // Crear una instancia de la clase que contiene la función
        $classInstance = new class {
            use prestamosTrait;
        };

        // Llamar a la función con el mock de Pspagos
        $resultado = $classInstance->getTotalintereses($request, $mockPspagos);

        // Verificar que la respuesta es un JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $resultado);

        // Decodificar el contenido de la respuesta JSON
        $responseData = $resultado->getData(true);

        // Verificar que el mensaje de error y el código sean los esperados
        $this->assertEquals('Error en la base de datos', $responseData['message']);
        $this->assertEquals(500, $responseData['errorCode']);
        $this->assertArrayHasKey('lineError', $responseData);
        $this->assertArrayHasKey('file', $responseData);
    }


    public function test_render_template_returns_correct_html()
    {
        // Simular request
        $request = new Request([
            'id_prestamo' => 1,
            'id_empresa' => '1'
        ]);

        // Simular consultaVariablesPrestamo
        $mockInstance = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        $mockInstance->shouldReceive('consultaVariablesPrestamo')
            ->with('1', 1)
            ->andReturn([
                (object) ['cliente' => 'Juan Pérez', 'monto' => 100000]
            ]);

        // Simular getPlantillasDocumentos
        $mockInstance->shouldReceive('getPlantillasDocumentos')
            ->with($request, Mockery::type(Pstdocplant::class))
            ->andReturn([
                (object) [
                    'id' => 1,
                    'nombre' => 'Contrato',
                    'plantilla_html' => 'Cliente: {cliente}, Monto: {monto} <!--QRTquery1QRT--> 
                <tr>
                    <td>[numero_cuota]</td>
                    <td>[fecha_pago]</td>
                    <td>$ [valor_pagar]</td>
                </tr>
                QRT-->'
                ]
            ]);

        // Simular replaceVariablesInTemplate
        $mockInstance->shouldReceive('replaceVariablesInTemplate')
            ->andReturnUsing(function ($template, $variables) {
                return str_replace(['{cliente}', '{monto}'], [$variables['cliente'], $variables['monto']], $template);
            });

        // Simular Psquerytabla
        $mockPsquerytabla = Mockery::mock(Psquerytabla::class);
        $mockPsquerytabla->shouldReceive('where')->with('codigo', 'query1')->andReturnSelf();
        $mockPsquerytabla->shouldReceive('where')->with('id_empresa', '1')->andReturnSelf();
        $mockPsquerytabla->shouldReceive('first')->andReturn((object) ['sql' => 'SELECT numero_cuota, fecha_pago, valor_pagar FROM cuotas WHERE id_prestamo = 1']);

        // Simular DB::select
        DB::shouldReceive('select')->with('SELECT numero_cuota, fecha_pago, valor_pagar FROM cuotas WHERE id_prestamo = 1')->andReturn([
            (object) ['numero_cuota' => 1, 'fecha_pago' => '2025-04-01', 'valor_pagar' => 50000],
            (object) ['numero_cuota' => 2, 'fecha_pago' => '2025-05-01', 'valor_pagar' => 50000]
        ]);

        // Simular el modelo Pstdocplant
        $mockPstdocplant = Mockery::mock(Pstdocplant::class);

        // Ejecutar la función
        $resultado = $mockInstance->renderTemplate($request, $mockPsquerytabla, $mockPstdocplant);

        // Verificaciones
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertEquals('Contrato', $resultado[0]['nombre']);
        $this->assertStringContainsString('Cliente: Juan Pérez, Monto: 100000', $resultado[0]['plantilla_html']);

        // Validar la correcta sustitución de variables en los bloques <!--QRT ... QRT-->
        $this->assertStringContainsString('<tr>', $resultado[0]['plantilla_html']);
        //$this->assertStringContainsString('<td>1</td><td>[numero_cuota]</td>', $resultado[0]['plantilla_html']);
        $this->assertStringContainsString('<td>[fecha_pago]</td>', $resultado[0]['plantilla_html']);
        $this->assertStringContainsString('<td>$ [valor_pagar]</td>', $resultado[0]['plantilla_html']);
        $this->assertStringContainsString('<td>[numero_cuota]</td>', $resultado[0]['plantilla_html']);
        $this->assertStringContainsString('<td>[fecha_pago]</td>', $resultado[0]['plantilla_html']);
    }



    public function test_get_plantillas_documentos()
    {
        $mockInstance = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();
        $request = new Request([
            'id_prestamo' => 1,
            'id_empresa' => '1'
        ]);
        $mockPstdocplant = Mockery::mock(Pstdocplant::class);
        $mockPstdocplant->shouldReceive('where')->with('id_empresa', '1')->andReturnSelf();
        $mockPstdocplant->shouldReceive('get')->andReturn(collect([
            (object) ['id' => 1, 'nombre' => 'Plantilla 1'],
            (object) ['id' => 2, 'nombre' => 'Plantilla 2']
        ]));
        $resultado = $mockInstance->getPlantillasDocumentos($request, $mockPstdocplant);
        $this->assertCount(2, $resultado);
        $this->assertEquals('Plantilla 1', $resultado[0]->nombre);
        $this->assertEquals('Plantilla 2', $resultado[1]->nombre);
    }

    public function test_total_prestado_hoy_returns_correct_value()
    {
        // Simular nit_empresa
        $id_empresa = '1';

        // Crear instancia simulada del trait
        $mockInstance = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // Simular getCapitalInicial
        $mockInstance->shouldReceive('getCapitalInicial')
            ->with($id_empresa, Mockery::type(Psempresa::class))
            ->andReturn(50000.00);

        // Simular getCapitalPrestado
        $mockInstance->shouldReceive('getCapitalPrestado')
            ->with($id_empresa, Mockery::type(Psprestamos::class))
            ->andReturn(70000.00);

        // Simular los modelos Psempresa y Psprestamos
        $mockPsempresa = Mockery::mock(Psempresa::class);
        $mockPsprestamos = Mockery::mock(Psprestamos::class);

        // Ejecutar la función
        $resultado = $mockInstance->totalPrestadoHoy($id_empresa, $mockPsempresa, $mockPsprestamos);

        // Verificaciones
        $this->assertIsFloat($resultado);
        $this->assertEquals(120000.00, $resultado);
    }



    public function test_guardar_prestamo_fechas()
    {
        // Simular Request
        $request = new Request([
            'id_empresa' => '1',
            'id_cliente' => 10,
            'valorpres' => 100000,
            'numcuotas' => 2,
            'porcint' => 5,
            'id_periodo_pago' => 1,
            'id_sistema_pago' => 'SIS01',
            'fec_inicial' => '01/01/2023',
            'id_cobrador' => 999,
            'id_usureg' => 100,
            'fecha' => '2023-01-01'
        ]);

        // Simular la clase que usa el trait
        $mockInstance = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        // Simular calcularCuota()
        $mockInstance->shouldReceive('calcularCuota')
            ->once()
            ->with($request, Mockery::type(Psperiodopago::class), Mockery::type(Pspstiposistemaprest::class))
            ->andReturn([
                'datosprestamo' => ['valor_cuota' => 50000],
                'tabla' => [
                    [
                        'fecha' => '2023-02-01',
                        'interes' => 50000,
                        't_pagomes' => 50000,
                        'ind_renovar' => 0
                    ],
                    [
                        'fecha' => '2023-03-01',
                        'interes' => 50000,
                        't_pagomes' => 50000,
                        'ind_renovar' => 0
                    ],
                ]
            ]);

        // Simular la inserción en psprestamos y devolver un ID ficticio
        DB::shouldReceive('table->insertGetId')
            ->once()
            ->withArgs(function ($data) {
                return isset($data['valorpres']) && $data['valorpres'] === 100000;
            })
            ->andReturn(1);

        // Simular la inserción en psfechaspago (dos veces)
        DB::shouldReceive('table->insert')
            ->times(2)
            ->withArgs(function ($insertData) {
                return isset($insertData['id_prestamo']) && $insertData['id_prestamo'] === 1;
            })
            ->andReturn(true);

        // Simular modelos
        $mockPsperiodopago = Mockery::mock(Psperiodopago::class);
        $mockPspstiposistemaprest = Mockery::mock(Pspstiposistemaprest::class);

        // Ejecutar la función
        $prestamoId = $mockInstance->guardarPrestamoFechas($request, $mockPsperiodopago, $mockPspstiposistemaprest);

        // Verificaciones
        $this->assertEquals(1, $prestamoId, 'Debe retornar el ID ficticio (1) que simulamos.');
    }

    public function test_render_template_correctly_processes_query_tags()
    {
        // Simular request
        $request = new Request([
            'id_prestamo' => 1,
            'id_empresa' => '1'
        ]);

        // Simular consultaVariablesPrestamo
        $mockInstance = Mockery::mock(PrestamosTraitTestDummy::class)->makePartial();

        $mockInstance->shouldReceive('consultaVariablesPrestamo')
            ->with('1', 1)
            ->andReturn([
                (object) ['cliente' => 'Juan Pérez', 'monto' => 100000]
            ]);

        // Simular getPlantillasDocumentos
        $mockInstance->shouldReceive('getPlantillasDocumentos')
            ->with($request, Mockery::type(Pstdocplant::class))
            ->andReturn([
                (object) [
                    'id' => 1,
                    'nombre' => 'Contrato',
                    'plantilla_html' => 'Cliente: {cliente}, Monto: {monto} <!--QRTquery1QRT--> [campo1]'
                ]
            ]);

        // Simular replaceVariablesInTemplate
        $mockInstance->shouldReceive('replaceVariablesInTemplate')
            ->andReturnUsing(function ($template, $variables) {
                return str_replace(['{cliente}', '{monto}'], [$variables['cliente'], $variables['monto']], $template);
            });

        // Simular Psquerytabla
        $mockPsquerytabla = Mockery::mock(Psquerytabla::class);
        $mockPsquerytabla->shouldReceive('where')->with('codigo', 'query1')->andReturnSelf();
        $mockPsquerytabla->shouldReceive('where')->with('id_empresa', '1')->andReturnSelf();
        $mockPsquerytabla->shouldReceive('first')->andReturn((object) ['sql' => 'SELECT "Valor Query" AS campo1']);

        // Simular DB::select
        DB::shouldReceive('select')->with('SELECT "Valor Query" AS campo1')->andReturn([
            (object) ['campo1' => 'Valor Query']
        ]);

        // Simular el modelo Pstdocplant
        $mockPstdocplant = Mockery::mock(Pstdocplant::class);

        // Ejecutar la función
        $resultado = $mockInstance->renderTemplate($request, $mockPsquerytabla, $mockPstdocplant);

        // Verificaciones
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertEquals('Contrato', $resultado[0]['nombre']);
        $this->assertStringContainsString('Cliente: Juan Pérez, Monto: 100000', $resultado[0]['plantilla_html']);
        $this->assertStringContainsString('Cliente: Juan Pérez, Monto: 100000  [campo1]<br>', $resultado[0]['plantilla_html']);

        // Validar la correcta sustitución de variables en los bloques <!--QRT ... QRT--> y dentro de []
        //$this->assertStringContainsString('Valor Query', $resultado[0]['plantilla_html']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
