<?php

$idEmpresa = isset($argv[1]) ? (int) $argv[1] : 1;
$conFirma = !isset($argv[2]) || $argv[2] !== '--sin-firma';

$variables = [
    'id_empresa' => $idEmpresa,
    'id_prestamo' => 245,
    'nombre' => 'JUAN PEREZ',
    'numcuotas' => 12,
    'valorpres' => '1,200,000.00',
    'valorpresf' => '1,200,000.00',
    'fecha_actual' => date('d/m/Y'),
    'hora_actua' => date('h:i:s A'),
    'nomfpago' => 'SEMANAL',
    'firma_cliente' => $conFirma
        ? 'http://147.93.1.252:8002/upload/documentosAdjuntos/3-1770861358.png'
        : '',
];

$plantilla = '
<div style="font-family:Arial,sans-serif;max-width:800px;margin:0 auto;line-height:1.45">
  <h2>Pagaré de Préstamo</h2>
  <p><b>Empresa:</b> {id_empresa}</p>
  <p><b>Préstamo:</b> {id_prestamo}</p>
  <p><b>Cliente:</b> {nombre}</p>
  <p><b>Valor:</b> ${valorpresf}</p>
  <p><b>Número de cuotas:</b> {numcuotas}</p>
  <p><b>Frecuencia:</b> {nomfpago}</p>
  <p><b>Fecha impresión:</b> {fecha_actual} {hora_actua}</p>

  <h3>Firma cliente</h3>
  <div style="min-height:90px;border:1px dashed #999;padding:8px">
    <img src="{firma_cliente}" alt="Firma cliente" style="max-height:80px;max-width:300px" />
  </div>
</div>';

$render = preg_replace_callback(
    '#{(.*?)}#',
    function ($match) use ($variables) {
        $key = trim($match[1], '$');
        return isset($variables[$key]) ? (string) $variables[$key] : '';
    },
    $plantilla
);

if (!$conFirma) {
    $render = str_replace('<img src="" alt="Firma cliente" style="max-height:80px;max-width:300px" />', '<span style="color:#777">SIN FIRMA REGISTRADA</span>', $render);
}

$outputPath = __DIR__ . '/../storage/app/render_prestamo_mock_' . $idEmpresa . ($conFirma ? '_con_firma' : '_sin_firma') . '.html';
if (!is_dir(dirname($outputPath))) {
    mkdir(dirname($outputPath), 0775, true);
}

$html = '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Mock render préstamo</title></head><body>' . $render . '</body></html>';
file_put_contents($outputPath, $html);

echo "OK\n";
echo "id_empresa={$idEmpresa}\n";
echo 'modo=' . ($conFirma ? 'con_firma' : 'sin_firma') . "\n";
echo "archivo_html={$outputPath}\n";
