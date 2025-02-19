<?php

namespace App\SwaggerAutoGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class GenerateSwaggerCommand extends Command
{
    protected $signature = 'swagger:generate';
    protected $description = 'Automatically generate Swagger documentation for Laravel API';

    public function handle(): void
    {
        $this->info('🔄 Đang quét API routes...');

        // TODO:lấy danh sách API từ routes
        $routes             = $this->getApiRoutes();
        $swaggerAnnotations = $this->generateSwaggerAnnotations($routes);

        $this->info('✅ Swagger documentation generated successfully!');
    }

    private function getApiRoutes()
    {
        $result = [];
        foreach (Route::getRoutes() as $route) {
            if (in_array($route->uri(), [
                'api/documentation',
                'docs',
                'docs/asset/{asset}',
                'api/oauth2-callback',
                'up',
                '/',
                'storage/{path}'
            ])) {
                continue;
            }
            $result[] = [
                'uri'    => $route->uri(),
                'method' => $route->methods(),
                'action' => $route->getActionName(),
            ];
        }
        return $result;
    }

    private function getResponseFromMethod(\ReflectionMethod $method, $controller)
    {
        $defaultResponse    = '';
        $controllerInstance = app($controller);                     // Khởi tạo instance controller
        $response           = $method->invoke($controllerInstance); // Gọi phương thức
        $data               = $response->getData();
        foreach ($data as $key => $value) {
            $type = gettype($value);
            if ($type === 'integer') {
                $type = 'int';
            }
            $defaultResponse .= "@OA\Property(property=\"$key\", type=\"$type\", example=\"$value\"),\n";
        }

        return rtrim($defaultResponse, ",\n");
    }

    private function generateSwaggerAnnotations($routes)
    {
        foreach ($routes as $index => $route) {
            [$controller, $method] = explode('@', $route['action']);

            if (!class_exists($controller) || !method_exists($controller, $method)) {
                continue;
            }
            $reflection = new \ReflectionMethod($controller, $method);

            // Lấy response mặc định từ function
            $response = $this->getResponseFromMethod($reflection, $controller);
            $swagger  = <<<EOD

        /**
         * @OA\Get(
         *     path="{$route['uri']}",
         *     summary="Lấy thông tin người dùng",
         *     description="Trả về thông tin cơ bản của người dùng.",
         *     tags={"Users"},
         *     @OA\Response(
         *         response=200,
         *         description="Thông tin người dùng",
         *         @OA\JsonContent(
         *             type="object",
         *             {$response}
         *         )
         *     )
         * )
         */

        EOD;


            // push vao controller action
            $filePath = $reflection->getFileName();
            $fileContent = file($filePath);
            $startLine  = $reflection->getStartLine() - 1;

            array_splice($fileContent, $startLine, 0, $swagger);
            file_put_contents($filePath, implode('', $fileContent));


        }

    }
}
