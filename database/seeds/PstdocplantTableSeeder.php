<?php

use Illuminate\Database\Seeder;

class PstdocplantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pstdocplant')->insert([


            'nombre' => 'Pagare',
            'plantilla_html' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Pagaré</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            line-height: 1.5;
        }
        h1, h2 {
            text-align: center;
        }
        .content {
            margin-top: 2rem;
        }
        .signature {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid black;
            margin-top: 2rem;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Pagaré</h1>
    <h2>Número de Préstamo: {id_prestamo}</h2>

    <div class=\"content\">
        <p>
            En la ciudad de {ciudad}, siendo la fecha {fecha_actual} y hora {hora_actua}, 
            yo, {nomcliente}, identificado(a) con el documento tipo {nomtipodocumento} número {numdocumento},
            me comprometo a pagar la suma de {valorpresf} (valor del préstamo) en {numcuotas} cuotas.
        </p>

        <p>
            Este documento certifica que el préstamo se encuentra registrado bajo el sistema de amortización identificado como {nomtipsistemap}, y las fechas y detalles de pago se reflejarán en el documento "Resumen del Préstamo".
        </p>

        <p>
            Los detalles completos del préstamo, como el monto, interés aplicado ({porcint}%), y los datos del pagador, 
            se encuentran debidamente registrados. Este pagaré será presentado para el cobro según lo estipulado.
        </p>

        <p>
            Datos del cliente:
            <ul>
                <li>Nombre completo: {nomcliente}</li>
                <li>Documento: {nomtipodocumento} - {numdocumento}</li>
                <li>Teléfono: {celular}</li>
                <li>Correo electrónico: {email}</li>
                <li>Dirección de residencia: {direcasa}</li>
            </ul>
        </p>

        <p>
            Datos del acreedor:
            <ul>
                <li>Nombre: {nombre}</li>
                <li>Dirección: {ddirec}</li>
                <li>Teléfono: {telefono}</li>
            </ul>
        </p>

        <p>
            Firmas:
        </p>

        <div class="signature">
            <div>
                ____________________________<br>
                Firma del Cliente<br>
                {nomcliente}
            </div>

            <div>
                ____________________________<br>
                Firma del Acreedor<br>
                {nombre}
            </div>
        </div>
    </div>
</body>
</html>',
            'id_empresa' => '1'

        ]);









        DB::table('pstdocplant')->insert([

            'nombre' => 'Resumen del prestamo',
            'plantilla_html' => '<!DOCTYPE html>
<html lang=\"es\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Resumen del Préstamo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Resumen del Préstamo</h1>
    <p><strong>Valor Prestado:</strong> $ {valorpresf}</p>
    <p><strong>Período de Pago:</strong> {nomperiodopago}</p>

    <h2>Fechas de Pago</h2>

    <table>
        <thead>
            <tr>
                <th>N° Cuota</th>
                <th>Fecha de Pago</th>
                <th>Total a Pagar por Cuota</th>
            </tr>
        </thead>
        <tbody>
            <!--QRT1
            <tr>
                <td>[numero_cuota]</td>
                <td>[fecha_pago]</td>
                <td>$ [valor_pagar]</td>
            </tr>
            QRT-->
        </tbody>
    </table>
</body>
</html>',
            'id_empresa' => '1'


        ]);
    }
}
