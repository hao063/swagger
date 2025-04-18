<?php

namespace App\SwaggerAutoGenerator\Commands;

use Illuminate\Console\Command;

class GenerateSwaggerCommand extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Automatically generate Swagger documentation for Laravel API';

    public function handle(): void
    {
        $this->generateSwaggerAnnotations();

        $this->info('✅ Swagger documentation generated successfully!');
    }

    private function generateSwaggerAnnotations(): void
    {
        $fileContents = [];
        $filePath     = storage_path('swagger/swagger-data.json');
        if (!file_exists($filePath)) {
            return;
        }
        $data = json_decode(file_get_contents($filePath), true);

        $controllers = array_unique(array_column($data, "controller"));
        foreach ($controllers as $controllerClass) {
            $filePath       = (new \ReflectionClass($controllerClass))->getFileName();
            $cleanedContent = $this->removeAllSwaggerComments($filePath);
            file_put_contents($filePath, $cleanedContent);
        }

        foreach ($data as $item) {
            $uri = $item['url'];
            [$parameters, $requestBody] = $this->generateSwaggerRequest(
                $item['method'],
                $item['path_params'],
                $item['query_params']
            );
            $security   = $this->buildSecurity($item['headers']);
            $controller = $item['controller'];
            $action     = $item['action'];
            $tags       = str_replace('Controller', '', class_basename($controller));
            $httpMethod = ucfirst(strtolower($item['method']));

            $lines = [
                "   /**",
                "    * @OA\\$httpMethod(",
                "    *     path=\"$uri\",",
            ];
            if (!empty($security)) {
                $lines = array_merge($lines, $security);
            }
            $lines = array_merge($lines, [
                "    *     summary=\"Swagger $controller@$action\",",
                "    *     description=\"\",",
                "    *     tags={\"$tags\"},",
            ]);
            if (!empty($parameters)) {
                $lines = array_merge($lines, $parameters);
            }

            if (!empty($requestBody)) {
                $lines[] = array_merge($lines, $parameters);
            }

            $lines    = array_merge($lines, [
                "    *     @OA\Response(",
                "    *         response=200,",
                "    *         description=\"Return information\",",
                "    *         @OA\JsonContent(",
                "    *             type=\"object\",",
            ]);
            $response = $this->generateSwaggerFromTree($item['response']);
            $lines[]  = $response;
            $lines[]  = "    *         )";
            $lines[]  = "    *     )";
            $lines[]  = "    * )";
            $lines[]  = "    */";
            $lines[]  = "";

            $swagger    = implode("\n", $lines);
            $reflection = new \ReflectionMethod($item['controller'], $item['action']);
            $filePath   = $reflection->getFileName();
            if (!isset($fileContents[$filePath])) {
                $dataContent = file($filePath);
            } else {
                $dataContent = $fileContents[$filePath];
            }
            $lines = [];
            foreach ($dataContent as $line) {
                if (preg_match('/function\s+'.$item['action'].'\s*\(/', $line)) {
                    $lines[] = $swagger;
                }
                $lines[] = $line;
            }
            $fileContents[$filePath] = $lines;
        }
        foreach ($fileContents as $path => $content) {
            file_put_contents($path, implode('', $content));
        }
    }


    private function generateSwaggerRequest(string $httpMethod, mixed $pathParams, mixed $queryParams): array
    {
        $requestBody = [];
        $paramDocs   = [];
        foreach ($pathParams as $keyName => $value) {
            $paramDocs[] = "    *     @OA\Parameter(";
            $paramDocs[] = "    *         name=\"$keyName\",";
            $paramDocs[] = "    *         in=\"path\",";
            $paramDocs[] = "    *         required=true,";
            $paramDocs[] = "    *         @OA\Schema(type=\"string\")";
            $paramDocs[] = "    *     ),";
        }

        if (!empty($queryParams)) {
            if (in_array(strtoupper($httpMethod), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $swaggerLines = $this->generateSwaggerFromTree($queryParams);
                $requestBody  = [
                    "*     @OA\RequestBody(",
                    "*         required=true,",
                    "*         @OA\JsonContent(",
                    "*             type=\"object\",",
                    $swaggerLines,
                    "*         )",
                    "*     ),"
                ];
            } else {
                foreach ($queryParams as $key => $value) {
                    $paramDocs[] = "    *     @OA\Parameter(";
                    $paramDocs[] = "    *         name=\"$key\",";
                    $paramDocs[] = "    *         in=\"query\",";
                    $paramDocs[] = "    *         required=false,";
                    $paramDocs[] = "    *         @OA\Schema(type=\"string\")";
                    $paramDocs[] = "    *     ),";
                }
            }
        }
        return [$paramDocs, $requestBody];
    }

    private function generateSwaggerFromTree($json, int $level = 2): string
    {
        $lines  = [];
        $indent = str_repeat('    ', $level);
        $prefix = '    *    '.$indent;
        foreach ($json as $key => $value) {
            if (is_array($value)) {
                if (isset($value[0])) {
                    if (is_array($value[0])) {
                        $lines[] = "{$prefix}@OA\Property(";
                        $lines[] = "{$prefix}    property=\"$key\",";
                        $lines[] = "{$prefix}    type=\"array\",";
                        $lines[] = "{$prefix}    @OA\Items(";
                        $lines[] = $this->generateSwaggerFromTree($value[0], $level + 2);
                        $lines[] = "{$prefix}    )";
                        $lines[] = "{$prefix}),";
                    } else {
                        $type    = $this->mapType(gettype($value[0]));
                        $lines[] = "{$prefix}@OA\Property(";
                        $lines[] = "{$prefix}    property=\"$key\",";
                        $lines[] = "{$prefix}    type=\"array\",";
                        $lines[] = "{$prefix}    @OA\Items(type=\"$type\")";
                        $lines[] = "{$prefix}),";
                    }
                } else {
                    $lines[] = "{$prefix}@OA\Property(";
                    $lines[] = "{$prefix}    property=\"$key\",";
                    $lines[] = "{$prefix}    type=\"array\",";
                    $lines[] = "{$prefix}    @OA\Items(type=\"string\")";
                    $lines[] = "{$prefix}),";
                }
            } elseif (is_object($value)) {
                $lines[] = "{$prefix}@OA\Property(";
                $lines[] = "{$prefix}    property=\"$key\",";
                $lines[] = "{$prefix}    type=\"object\",";
                $lines[] = $this->generateSwaggerFromTree((array)$value, $level + 1);
                $lines[] = "{$prefix}),";
            } else {
                $type    = $this->mapType(gettype($value));
                $lines[] = "{$prefix}@OA\\Property(property=\"$key\", type=\"$type\"),";
            }
        }
        return implode("\n", $lines);
    }

    private function mapType($type): string
    {
        return match ($type) {
            'integer' => 'integer',
            'double'  => 'number',
            'boolean' => 'boolean',
            'array'   => 'array',
            default   => 'string'
        };
    }

    private function removeAllSwaggerComments(string $filePath): string
    {
        $lines  = file($filePath);
        $output = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_contains($trimmed, '*')) {
                continue;
            }
            $output[] = $line;
        }

        return implode('', $output);
    }

    private function buildSecurity(mixed $headers): array
    {
        if (empty($headers)) {
            return [];
        }
        $lines   = [];
        $lines[] = "    *     security={{";
        foreach ($headers as $header) {
            $lines[] = "    *         \"{$header}\": {},";
        }
        $lines[] = "    *       }},";
        return $lines;
    }
}
