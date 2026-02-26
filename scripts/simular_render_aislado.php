<?php

// Uso:
// php scripts/simular_render_aislado.php
// php scripts/simular_render_aislado.php --sin-firma
// php scripts/simular_render_aislado.php --id-empresa=1 --id-prestamo=1

function replaceVariablesInTemplate($template, array $variables)
{
    return preg_replace_callback(
        '#{(.*?)}#',
        function ($match) use ($variables) {
            $key = trim($match[1], '$');
            return isset($variables[$key]) ? (string) $variables[$key] : '';
        },
        $template
    );
}

function setVars($queryRows, $variables, $template, $acumulador)
{
    foreach ($queryRows as $objeto) {
        $cadenaReemplazada = $template;

        foreach ($variables as $variable) {
            $valor = isset($objeto[$variable]) ? $objeto[$variable] : '';
            $placeholder = '[' . $variable . ']';
            $cadenaReemplazada = str_replace($placeholder, (string) $valor, $cadenaReemplazada);
        }

        $acumulador .= $cadenaReemplazada;
    }

    return $acumulador;
}

function renderTemplate2($matches, $queryTablaMap, $renderTemplate)
{
    $str2 = '';

    foreach ($matches as $value) {
        $qt = $value[0];
        $str = ltrim($value, $qt);

        if (preg_match_all('/' . preg_quote('[') . '(.*?)' . preg_quote(']') . '/s', $str, $matchesv)) {
            $queryRows = isset($queryTablaMap[$qt]) ? $queryTablaMap[$qt] : [];
            $variables = $matchesv[1];
            $str2 = setVars($queryRows, $variables, $str, $str2);
        }

        $renderTemplate = str_replace($matches[0], $str2, $renderTemplate);
        $renderTemplate = str_replace('<!--QRT', '', $renderTemplate);
        $renderTemplate = str_replace('QRT-->', '', $renderTemplate);
    }

    return $renderTemplate;
}

$args = $argv;
array_shift($args);

$idEmpresa = 1;
$idPrestamo = 1;
$sinFirma = false;
$baseUrl = 'http://147.93.1.252:8002';

foreach ($args as $arg) {
    if ($arg === '--sin-firma') {
        $sinFirma = true;
        continue;
    }

    if (strpos($arg, '--id-empresa=') === 0) {
        $idEmpresa = (int) substr($arg, strlen('--id-empresa='));
        continue;
    }

    if (strpos($arg, '--id-prestamo=') === 0) {
        $idPrestamo = (int) substr($arg, strlen('--id-prestamo='));
        continue;
    }

    if (strpos($arg, '--base-url=') === 0) {
        $baseUrl = rtrim((string) substr($arg, strlen('--base-url=')), '/');
        continue;
    }
}

if ($idEmpresa <= 0 || $idPrestamo <= 0) {
    fwrite(STDERR, "Parámetros inválidos.\n");
    exit(1);
}

// Simulación de salida del query principal
$variablesPrestamo = [
    'fecha_actual' => date('d/m/Y'),
    'hora_actua' => date('H:i:s') . ' PM',
    'id_prestamo' => $idPrestamo,
    'valorpresf' => '1,000,000.00',
    'id_cliente' => 1,
    'id_tipo_sistema_prest' => 1,
    'valorpres' => '1000000.0',
    'numcuotas' => 12,
    'id_periodo_pago' => 1,
    'valcuota' => '88848.76',
    'porcint' => '12.0',
    'fec_inicial' => '2026-02-13',
    'id_cobrador' => 2,
    'id_usureg' => 1,
    'ind_estado' => 1,
    'id_empresa' => $idEmpresa,
    'nomcliente' => 'Cliente de Prueba',
    'nomtipodocumento' => 'Cédula',
    'numdocumento' => '12345678',
    'ciudad' => 'Santiago de cali',
    'celular' => '3184559635',
    'direcasa' => 'Calle 73 1f-45 barrio gaitan',
    'email' => 'usuarioprueba@gmail.com',
    'nombre' => 'Empresa de pruebas',
    'ddirec' => 'Calle xx # yy-zz',
    'telefono' => '3184469889',
    'nomtipsistemap' => 'Sistema Frances',
    'nomperiodopago' => 'Diario',
    'nomfpago' => 'Diario',
    'firma_cliente' => $sinFirma
        ? ''
        : $baseUrl . '/upload/documentosAdjuntos/3-1770861358.png',
];

// Simulación de salida de psquerytabla para QRT1 (fechas de pago)
$queryTablaMap = [
    '1' => [
        ['numero_cuota' => 1, 'fecha_pago' => '14/02/2026', 'valor_pagar' => '88,848.76'],
        ['numero_cuota' => 2, 'fecha_pago' => '15/02/2026', 'valor_pagar' => '88,848.76'],
        ['numero_cuota' => 3, 'fecha_pago' => '16/02/2026', 'valor_pagar' => '88,848.76'],
        ['numero_cuota' => 4, 'fecha_pago' => '17/02/2026', 'valor_pagar' => '88,848.76'],
    ],
];

// Plantilla 1: Pagaré (la misma que compartiste + img de firma cliente)
$plantillaPagare = <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <div class="content">
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
                <img src="{firma_cliente}" alt="Firma cliente" style="max-height:80px; display:block; margin:0 auto 6px auto;" />
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
</html>
HTML;

// Plantilla 2: Resumen del préstamo (la misma que compartiste)
$plantillaResumen = <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</html>
HTML;

$renderPagare = replaceVariablesInTemplate($plantillaPagare, $variablesPrestamo);

if ($sinFirma) {
    $renderPagare = str_replace(
        '<img src="" alt="Firma cliente" style="max-height:80px; display:block; margin:0 auto 6px auto;" />',
        '<span style="color:#6b7280; display:block; margin-bottom:6px;">SIN FIRMA REGISTRADA</span>',
        $renderPagare
    );
}

$renderResumen = replaceVariablesInTemplate($plantillaResumen, $variablesPrestamo);

if (preg_match_all('/' . preg_quote('<!--QRT') . '(.*?)' . preg_quote('QRT-->') . '/s', $renderResumen, $matches)) {
    $renderResumen = renderTemplate2($matches[1], $queryTablaMap, $renderResumen);
}

$baseOut = __DIR__ . '/../storage/app';
if (!is_dir($baseOut)) {
    mkdir($baseOut, 0775, true);
}

$modo = $sinFirma ? 'sin_firma' : 'con_firma';
$outPagare = $baseOut . '/render_prestamo_aislado_pagare_' . $idEmpresa . '_' . $idPrestamo . '_' . $modo . '.html';
$outResumen = $baseOut . '/render_prestamo_aislado_resumen_' . $idEmpresa . '_' . $idPrestamo . '_' . $modo . '.html';
$outIndex = $baseOut . '/render_prestamo_aislado_index_' . $idEmpresa . '_' . $idPrestamo . '_' . $modo . '.html';

file_put_contents($outPagare, $renderPagare);
file_put_contents($outResumen, $renderResumen);

$indexHtml = '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Render aislado</title></head><body>';
$indexHtml .= '<h2>Render aislado de plantillas</h2>';
$indexHtml .= '<p><b>id_empresa:</b> ' . $idEmpresa . ' | <b>id_prestamo:</b> ' . $idPrestamo . ' | <b>modo:</b> ' . $modo . '</p>';
$indexHtml .= '<p><b>firma_cliente:</b> ' . ($variablesPrestamo['firma_cliente'] !== '' ? $variablesPrestamo['firma_cliente'] : 'SIN_FIRMA') . '</p>';
$indexHtml .= '<ul>';
$indexHtml .= '<li><a href="' . basename($outPagare) . '">Abrir Pagaré</a></li>';
$indexHtml .= '<li><a href="' . basename($outResumen) . '">Abrir Resumen del préstamo</a></li>';
$indexHtml .= '</ul>';
$indexHtml .= '<hr><h3>Vista rápida Pagaré</h3>' . $renderPagare;
$indexHtml .= '<hr><h3>Vista rápida Resumen</h3>' . $renderResumen;
$indexHtml .= '</body></html>';

file_put_contents($outIndex, $indexHtml);

echo "OK\n";
echo "id_empresa={$idEmpresa}\n";
echo "id_prestamo={$idPrestamo}\n";
echo "modo={$modo}\n";
echo 'firma_cliente=' . ($variablesPrestamo['firma_cliente'] !== '' ? $variablesPrestamo['firma_cliente'] : 'SIN_FIRMA') . "\n";
echo "archivo_pagare={$outPagare}\n";
echo "archivo_resumen={$outResumen}\n";
echo "archivo_index={$outIndex}\n";
