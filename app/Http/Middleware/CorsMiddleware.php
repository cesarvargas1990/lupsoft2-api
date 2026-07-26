<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:4200',
            'http://127.0.0.1:4200',

            // Agrega aquí tu frontend de producción:
            // 'https://tudominio.com',
        ];

        $origin = $request->headers->get('Origin');

        $headers = [
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Origin, Content-Type, Accept, Authorization, X-Requested-With, X-CSRF-TOKEN',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Vary'                             => 'Origin',
        ];

        if ($origin && in_array($origin, $allowedOrigins, true)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
        }

        // Responder directamente al preflight del navegador
        if ($request->isMethod('OPTIONS')) {
            return response('', 204, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
