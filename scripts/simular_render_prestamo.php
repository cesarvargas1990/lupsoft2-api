<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$idEmpresa = isset($argv[1]) ? (int) $argv[1] : 1;
$idPrestamo = isset($argv[2]) ? (int) $argv[2] : null;
$baseUrl = isset($argv[3]) ? rtrim($argv[3], '/') : rtrim((string) env('APP_URL', 'http://127.0.0.1:8002'), '/');

if ($idEmpresa <= 0) {
    fwrite(STDERR, "id_empresa inválido.\n");
    exit(1);
}

if (empty($idPrestamo)) {
    $prestamo = \Illuminate\Support\Facades\DB::table('psprestamos')
        ->where('id_empresa', $idEmpresa)
        ->where('ind_estado', 1)
        ->orderBy('id', 'desc')
        ->first();

    if (!$prestamo) {
        fwrite(STDERR, "No se encontró préstamo activo para id_empresa={$idEmpresa}.\n");
        exit(1);
    }

    $idPrestamo = (int) $prestamo->id;
}

$request = new \Illuminate\Http\Request();
$request->request->add([
    'id_empresa' => $idEmpresa,
    'id_prestamo' => $idPrestamo,
]);

$renderer = new class {
    use \App\Http\Traits\General\prestamosTrait;
};

try {
    $variables = $renderer->consultaVariablesPrestamo($idEmpresa, $idPrestamo);
    if (empty($variables)) {
        fwrite(STDERR, "No se encontraron variables para id_prestamo={$idPrestamo}.\n");
        exit(1);
    }

    $vars = json_decode(json_encode($variables[0]), true);
    $firmaRelativa = isset($vars['firma_cliente']) ? trim((string) $vars['firma_cliente']) : '';
    $firmaAbsoluta = '';
    if ($firmaRelativa !== '') {
        $firmaAbsoluta = preg_match('#^https?://#i', $firmaRelativa)
            ? $firmaRelativa
            : $baseUrl . '/' . ltrim($firmaRelativa, '/');
    }

    $data = $renderer->renderTemplate($request, new \App\Psquerytabla(), new \App\Pstdocplant());

    $rows = [];
    foreach ($data as $documento) {
        $html = (string) $documento['plantilla_html'];
        if ($firmaRelativa !== '') {
            $html = str_replace('src="' . $firmaRelativa . '"', 'src="' . $firmaAbsoluta . '"', $html);
            $html = str_replace("src='" . $firmaRelativa . "'", "src='" . $firmaAbsoluta . "'", $html);
            $html = str_replace($firmaRelativa, $firmaAbsoluta, $html);
        }

        $rows[] = [
            'id' => $documento['id'],
            'nombre' => $documento['nombre'],
            'plantilla_html' => $html,
        ];
    }

    $outputPath = __DIR__ . '/../storage/app/render_prestamo_' . $idEmpresa . '_' . $idPrestamo . '.html';

    $fullHtml = '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Render préstamo</title></head><body>';
    $fullHtml .= '<h2>Simulación render préstamo</h2>';
    $fullHtml .= '<p><b>id_empresa:</b> ' . $idEmpresa . ' | <b>id_prestamo:</b> ' . $idPrestamo . '</p>';
    $fullHtml .= '<p><b>firma_cliente (ruta):</b> ' . ($firmaRelativa !== '' ? htmlspecialchars($firmaRelativa, ENT_QUOTES, 'UTF-8') : 'SIN FIRMA') . '</p>';
    $fullHtml .= '<p><b>firma_cliente (url):</b> ' . ($firmaAbsoluta !== '' ? htmlspecialchars($firmaAbsoluta, ENT_QUOTES, 'UTF-8') : 'SIN FIRMA') . '</p>';

    if ($firmaAbsoluta !== '') {
        $fullHtml .= '<div><b>Vista previa firma:</b><br><img src="' . htmlspecialchars($firmaAbsoluta, ENT_QUOTES, 'UTF-8') . '" style="max-width:300px;border:1px solid #ccc"></div><hr>';
    }

    foreach ($rows as $item) {
        $fullHtml .= '<h3>Plantilla #' . (int) $item['id'] . ' - ' . htmlspecialchars((string) $item['nombre'], ENT_QUOTES, 'UTF-8') . '</h3>';
        $fullHtml .= '<div>' . $item['plantilla_html'] . '</div><hr>';
    }

    $fullHtml .= '</body></html>';

    if (!is_dir(dirname($outputPath))) {
        mkdir(dirname($outputPath), 0775, true);
    }

    file_put_contents($outputPath, $fullHtml);

    echo "OK\n";
    echo "id_empresa={$idEmpresa}\n";
    echo "id_prestamo={$idPrestamo}\n";
    echo 'firma_cliente=' . ($firmaRelativa !== '' ? $firmaRelativa : 'SIN_FIRMA') . "\n";
    echo "archivo_html={$outputPath}\n";
} catch (\Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
