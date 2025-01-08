<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

$modules = config('module.modules');
$path = config('module.path');
$baseNamespace = config('module.base_namespace');

if ($modules) {
    foreach ($modules as $module => $options) {
        $modulePath = $path . '/' . $module;
        $moduleNamespace = $baseNamespace . $module;

        if (is_dir($modulePath)) {
            $routesPath = $modulePath . '/Routes/api.php';
            if (file_exists($routesPath)) {
                Route::middleware($options['middleware'] ?? [])
                    ->group(function () use ($routesPath, $moduleNamespace) {
                        require $routesPath;
                });
            }
        }
    }
}
