<?php

namespace App\Providers;

use App\SwaggerAutoGenerator\Commands\GenerateSwaggerCommand;
use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            GenerateSwaggerCommand::class
        ]);
    }
}