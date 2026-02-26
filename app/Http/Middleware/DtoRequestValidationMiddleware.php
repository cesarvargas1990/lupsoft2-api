<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DtoRequestValidationMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (strtoupper($request->method()) === 'OPTIONS') {
            return $next($request);
        }

        $map = app()->bound('route.dto.map') ? app('route.dto.map') : [];
        if (!is_array($map) || empty($map)) {
            return $next($request);
        }

        $matched = $this->matchContract($request, $map);
        if ($matched === null) {
            return $next($request);
        }

        $paramErrors = $this->validateParams($matched['params'], isset($matched['contract']['params']) ? $matched['contract']['params'] : []);
        if (!empty($paramErrors)) {
            return response()->json([
                'message' => 'Route params validation failed',
                'errors' => $paramErrors,
                'contract' => $matched['endpoint'],
            ], 422);
        }

        $bodyContract = isset($matched['contract']['body']) ? $matched['contract']['body'] : null;
        if ($bodyContract === null) {
            $input = $request->all();
            if (!empty($input)) {
                return response()->json([
                    'message' => 'Request body is not allowed for this endpoint',
                    'errors' => ['body must be empty'],
                    'contract' => $matched['endpoint'],
                ], 422);
            }
        }

        if (is_string($bodyContract) && strpos($bodyContract, 'object{') === 0) {
            $bodyErrors = $this->validateBody($request, $bodyContract);
            if (!empty($bodyErrors)) {
                return response()->json([
                    'message' => 'Request body validation failed',
                    'errors' => $bodyErrors,
                    'contract' => $matched['endpoint'],
                ], 422);
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @param array<string, array<string, mixed>> $map
     * @return array<string, mixed>|null
     */
    private function matchContract(Request $request, array $map)
    {
        $method = strtoupper($request->method());
        $path = trim((string) $request->path(), '/');
        $pathWithSlash = '/' . $path;
        if ($path === '') {
            $pathWithSlash = '/';
        }

        // Fast path: exact key match (most reliable).
        $directKey = $method . ' ' . $pathWithSlash;
        if (isset($map[$directKey])) {
            return [
                'endpoint' => $directKey,
                'contract' => $map[$directKey],
                'params' => [],
            ];
        }

        foreach ($map as $endpoint => $contract) {
            $parts = explode(' ', $endpoint, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $endpointMethod = strtoupper($parts[0]);
            $template = trim($parts[1], '/');
            if ($endpointMethod !== $method) {
                continue;
            }

            $regex = $this->templateToRegex($template);
            if (preg_match($regex, $path, $matches) !== 1) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }

            return [
                'endpoint' => $endpoint,
                'contract' => $contract,
                'params' => $params,
            ];
        }

        return null;
    }

    /**
     * @param string $template
     * @return string
     */
    private function templateToRegex($template)
    {
        $escaped = preg_quote($template, '#');
        $regex = preg_replace_callback('/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^\\\\]+))?\\\\\}/', function ($m) {
            $name = $m[1];
            $pattern = isset($m[2]) && $m[2] !== '' ? $m[2] : '[^/]+';
            return '(?P<' . $name . '>' . $pattern . ')';
        }, $escaped);

        return '#^' . $regex . '/?$#';
    }

    /**
     * @param array<string, string> $params
     * @param array<string, string> $paramContract
     * @return array<int, string>
     */
    private function validateParams(array $params, array $paramContract)
    {
        $errors = [];

        foreach ($paramContract as $name => $type) {
            if (!array_key_exists($name, $params)) {
                $errors[] = $name . ' is required';
                continue;
            }

            if (!$this->isValidType($params[$name], $type)) {
                $errors[] = $name . ' must be ' . $type;
            }
        }

        return $errors;
    }

    /**
     * @param Request $request
     * @param string $bodyContract
     * @return array<int, string>
     */
    private function validateBody(Request $request, $bodyContract)
    {
        $errors = [];
        $rules = $this->parseObjectContract($bodyContract);
        $input = $request->all();

        foreach ($rules as $field => $rule) {
            if (!array_key_exists($field, $input)) {
                if (!empty($rule['required'])) {
                    $errors[] = $field . ' is required';
                }
                continue;
            }

            $value = $input[$field];
            if ($value === null && !empty($rule['nullable'])) {
                continue;
            }

            if (!$this->isValidType($value, $rule['type'])) {
                $errors[] = $field . ' must be ' . $rule['type'];
            }
        }

        return $errors;
    }

    /**
     * @param string $contract
     * @return array<string, array<string, mixed>>
     */
    private function parseObjectContract($contract)
    {
        $start = strpos($contract, '{');
        $end = strrpos($contract, '}');
        if ($start === false || $end === false || $end <= $start) {
            return [];
        }

        $inner = substr($contract, $start + 1, $end - $start - 1);
        $inner = trim($inner);
        if ($inner === '') {
            return [];
        }

        $parts = preg_split('/,(?![^<]*>)/', $inner);
        $rules = [];
        foreach ($parts as $part) {
            $pair = explode(':', trim($part), 2);
            if (count($pair) !== 2) {
                continue;
            }

            $rawField = trim($pair[0]);
            $type = trim($pair[1]);
            if ($rawField === '' || $type === '') {
                continue;
            }

            $required = true;
            if (substr($rawField, -1) === '?') {
                $required = false;
                $rawField = substr($rawField, 0, -1);
            }

            $nullable = false;
            if (strpos($type, '|null') !== false || strpos($type, 'null|') !== false || $type === 'null') {
                $nullable = true;
                $type = str_replace('|null', '', $type);
                $type = str_replace('null|', '', $type);
                $type = trim($type);
                if ($type === '') {
                    $type = 'mixed';
                }
            }

            $rules[$rawField] = [
                'type' => $type,
                'required' => $required,
                'nullable' => $nullable,
            ];
        }

        return $rules;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    private function isValidType($value, $type)
    {
        $union = explode('|', $type);
        foreach ($union as $singleType) {
            $singleType = trim($singleType);
            if ($singleType === '') {
                continue;
            }
            if ($this->isSingleTypeValid($value, $singleType)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    private function isSingleTypeValid($value, $type)
    {
        if (strpos($type, 'array<') === 0 && substr($type, -1) === '>') {
            if (!is_array($value)) {
                return false;
            }
            $innerType = substr($type, 6, -1);
            foreach ($value as $item) {
                if (!$this->isValidType($item, $innerType)) {
                    return false;
                }
            }
            return true;
        }

        switch ($type) {
            case 'int':
                return filter_var($value, FILTER_VALIDATE_INT) !== false;
            case 'float':
                return is_float($value) || is_int($value) || (is_string($value) && is_numeric($value));
            case 'string':
                return is_string($value);
            case 'bool':
                return is_bool($value) || $value === 0 || $value === 1 || $value === '0' || $value === '1';
            case 'null':
                return $value === null;
            case 'object':
                return is_array($value) || is_object($value);
            case 'mixed':
                return true;
            default:
                return true;
        }
    }
}
