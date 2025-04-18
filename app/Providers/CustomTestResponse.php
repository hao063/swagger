<?php

namespace App\Providers;

use Illuminate\Testing\TestResponse;

class CustomTestResponse extends TestResponse
{
    public function assertJsonStructure(array $structure = null, $responseData = null): static
    {
        parent::assertJsonStructure($structure, $responseData);
        $route      = request()->route();
        $actionName = $route->getActionName();
        [$controllerClass, $action] = explode('@', $actionName);
        $requestInformation = [
            'url'          => '/'.$route->uri,
            'method'       => request()->method(),
            'controller'   => $controllerClass,
            'action'       => $action,
            'headers'      => array_keys(request()->headers->all()),
            'path_params'  => $this->extractPathParams($route->uri, request()->uri()->path()),
            'query_params' => $this->simplifyJsonStructure(request()->all()),
            'response'     => $this->simplifyJsonStructure($this->json())
        ];

        $directory = storage_path('swagger');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $filePath                  = $directory.'/swagger-data.json';
        $existingData              = file_exists($filePath)
            ? json_decode(file_get_contents($filePath), true)
            : [];
        $existingData[$actionName] = $requestInformation;
        file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $this;
    }

    public static function fromBaseResponse($response, $request = null): static
    {
        return new static($response);
    }

    private function extractPathParams(string $routeUriPattern, string $actualPath): array
    {
        $routeSegments  = explode('/', trim($routeUriPattern, '/'));
        $actualSegments = explode('/', trim($actualPath, '/'));
        $params         = [];
        foreach ($routeSegments as $i => $segment) {
            if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $paramName          = $matches[1] ?? null;
                $params[$paramName] = $actualSegments[$i] ?? null;
            }
        }

        return $params;
    }

    private function simplifyJsonStructure(mixed $data): mixed
    {
        if (is_array($data)) {
            if (array_keys($data) === range(0, count($data) - 1)) {
                return isset($data[0]) ? [$this->simplifyJsonStructure($data[0])] : [];
            }

            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->simplifyJsonStructure($value);
            }
            return $result;
        }
        return $data;
    }
}
