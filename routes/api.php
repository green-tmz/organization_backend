<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

$modules = config('module.modules');
$pathModule = config('module.path');
$baseNamespace = config('module.base_namespace');
$moduleAr = [];

$exception = ['middleware'];

if ($modules) {
    foreach ($modules as $module => $options) {
        $namespace = $baseNamespace . $module;
        $path = $pathModule . '/' . $module;

        if (!empty($options)) {
            foreach ($options as $key => $option) {
                if (in_array($key, $exception)) {
                    $moduleAr[] = [
                        'path' => $path,
                        'namespace' => $namespace,
                        'middleware' => $options['middleware']?? [],
                    ] ;
                    break;
                }
                $moduleAr[] = [
                    'path' => $path . '/' . $key,
                    'namespace' => $namespace . '\\'. $key,
                    'middleware' => $option['middleware']?? [],
                ] ;
            }
        } else {
            $moduleAr[] = [
                'path' => $path,
                'namespace' => $namespace,
                'middleware' => $module['middleware']?? [],
            ] ;
        }
    }

    foreach ($moduleAr as $module) {
        if (is_dir($module['path'])) {
            $routesPath = $module['path'] . '/Routes/api.php';
            if (file_exists($routesPath)) {
                $moduleNamespace = $module['namespace'];
                Route::middleware($options['middleware'] ?? [])
                    ->group(function () use ($routesPath, $moduleNamespace) {
                        require $routesPath;
                });
            }
        }
    }
}
