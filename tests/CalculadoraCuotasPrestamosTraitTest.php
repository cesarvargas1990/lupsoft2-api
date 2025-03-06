<?php

namespace Tests\Unit;
use Mockery;
use TestCase;
use Illuminate\Http\Request;
use App\Psperiodopago;
use App\Pspstiposistemaprest;
use Carbon\Carbon;

class CalculadoraCuotasPrestamosTraitTest extends TestCase
{
   
    public function test_adicionar_fechas_returns_correct_date()
    {
        $classInstance = new class {
            use \App\Http\Traits\General\calculadoraCuotasPrestamosTrait;
        };

        $date = Carbon::create(2023, 1, 1);
        $this->assertEquals('2023-01-02', $classInstance->adicionarFechas($date, 1)); // 1 día
        $this->assertEquals('2023-01-09', $classInstance->adicionarFechas($date, 2)); // 7 días
        $this->assertEquals('2023-01-24', $classInstance->adicionarFechas($date, 3)); // 15 días
        $this->assertEquals('2023-02-24', $classInstance->adicionarFechas($date, 4)); // 1 mes
        $this->assertEquals('2024-02-24', $classInstance->adicionarFechas($date, 5)); // 1 año
        $this->assertEquals(null, $classInstance->adicionarFechas($date, 6)); // 1 año
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
