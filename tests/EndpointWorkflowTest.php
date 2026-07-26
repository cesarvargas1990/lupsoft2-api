<?php

use App\Http\Controllers\PsclientesController;
use App\Http\Controllers\PstdocplantController;
use App\Psclientes;
use App\Psdocadjuntos;
use App\Psfechaspago;
use App\Pspagos;
use App\Psprestamos;
use App\Pstdocadjuntos;
use App\Pstdocplant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class EndpointWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareSqliteDatabase();
        $this->artisan('migrate:fresh');
        $this->seedBaseCatalogs();

        $this->assertTrue(Schema::hasTable('pstdocplant'));
        $this->assertTrue(Schema::hasTable('psclientes'));
    }

    public function test_pstdocplant_create_and_delete_workflow()
    {
        $controller = new PstdocplantController();
        $request = new Request([
            'nombre' => 'Plantilla test pipeline',
            'plantilla_html' => '<p>contenido de prueba</p>',
            'id_empresa' => 1,
        ]);

        $createResponse = $controller->create($request, new Pstdocplant());
        $this->assertStatusCode(201, $createResponse, 'PstdocplantController@create');
        $created = json_decode($createResponse->getContent(), true);
        $this->assertIsArray($created);
        $this->assertArrayHasKey('id', $created);
        $id = (int) $created['id'];

        $this->seeInDatabase('pstdocplant', [
            'id' => $id,
            'nombre' => 'Plantilla test pipeline',
            'id_empresa' => 1,
        ]);

        $deleteResponse = $controller->delete($id, new Pstdocplant());
        $this->assertStatusCode(200, $deleteResponse, 'PstdocplantController@delete');

        $this->notSeeInDatabase('pstdocplant', ['id' => $id]);
    }

    public function test_psclientes_create_and_soft_delete_workflow()
    {
        $controller = new PsclientesController();
        $request = new Request([
            'nomcliente' => 'Cliente test delete',
            'id_tipo_docid' => 1,
            'numdocumento' => 'DOC-TEST-001',
            'id_empresa' => 1,
            'id_cobrador' => 2,
            'id_user' => 1,
            'email' => 'cliente.test@example.com',
            'fch_expdocumento' => '2026-02-27',
            'fch_nacimiento' => '1990-01-01',
            'ind_estado' => 1,
        ]);

        $createResponse = $controller->create($request, new Psclientes());
        $this->assertStatusCode(201, $createResponse, 'PsclientesController@create');
        $created = json_decode($createResponse->getContent(), true);
        $this->assertIsArray($created);
        $this->assertArrayHasKey('id', $created);
        $id = (int) $created['id'];

        $this->seeInDatabase('psclientes', [
            'id' => $id,
            'nomcliente' => 'Cliente test delete',
            'ind_estado' => 1,
        ]);

        $deleteResponse = $controller->delete(
            $id,
            new Psclientes(),
            new Psprestamos(),
            new Pspagos(),
            new Psfechaspago()
        );
        $this->assertStatusCode(200, $deleteResponse, 'PsclientesController@delete');

        // El endpoint hace soft delete por negocio: marca ind_estado = 0.
        $this->seeInDatabase('psclientes', [
            'id' => $id,
            'ind_estado' => 0,
        ]);
    }

    private function assertStatusCode($expected, $response, $endpoint)
    {
        $actual = $response->getStatusCode();
        $body = (string) $response->getContent();

        $this->assertSame(
            $expected,
            $actual,
            sprintf('%s expected HTTP %d got %d. Body: %s', $endpoint, $expected, $actual, $body)
        );
    }

    private function seedBaseCatalogs()
    {
        DB::table('psempresa')->insert([
            'id' => 1,
            'nombre' => 'Empresa test',
            'nitempresa' => 'NIT-TEST',
            'ddirec' => 'Direccion test',
            'ciudad' => 'Cali',
            'telefono' => '000000',
            'pagina' => 'https://example.test',
            'email' => 'empresa@test.com',
            'vlr_capinicial' => 1000000,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Administrador Test',
                'email' => 'admin@admin.com',
                'password' => app('hash')->make('password'),
                'id_empresa' => 1,
                'is_admin' => 1,
                'id_user' => null,
                'ind_activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Cobrador Test',
                'email' => 'cobrador@test.com',
                'password' => app('hash')->make('password'),
                'id_empresa' => 1,
                'is_admin' => 0,
                'id_user' => 1,
                'ind_activo' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        DB::table('pstipodocidenti')->insert([
            'id' => 1,
            'codtipdocid' => 13,
            'nomtipodocumento' => 'Cedula',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('pstdocadjuntos')->insert([
            'id' => 1,
            'nombre' => 'Documento base',
            'id_empresa' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function prepareSqliteDatabase()
    {
        $databasePath = env('DB_DATABASE');
        if (!$databasePath) {
            return;
        }

        $directory = dirname($databasePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!file_exists($databasePath)) {
            touch($databasePath);
        }
    }
}
