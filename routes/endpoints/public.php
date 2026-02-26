<?php

$router->get('/upload/documentosAdjuntos/{filepath:.*}', $dtoClosure(function ($filepath) {
    $safePath = str_replace('\\', '/', $filepath);
    if (strpos($safePath, '..') !== false) {
        return response()->json(['error' => 'Invalid path'], 400);
    }

    $basePath = base_path('upload/documentosAdjuntos');
    $file = $basePath . '/' . ltrim($safePath, '/');
    $realBasePath = realpath($basePath);
    $realFilePath = realpath($file);

    if ($realBasePath && $realFilePath && strpos($realFilePath, $realBasePath) === 0 && is_file($realFilePath)) {
        $mimeType = function_exists('mime_content_type') ? mime_content_type($realFilePath) : null;
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        return response(file_get_contents($realFilePath), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($realFilePath) . '"',
            'Content-Length' => filesize($realFilePath),
        ]);
    }

    return response()->json(['error' => 'File not found'], 404);
}));

$router->get('/', $dtoClosure(function () use ($router) {
    return $router->app->version();
}));
