<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModule extends Command implements PromptsForMissingInput
{
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {module}
                                        {--M|model : Create a model for the module}
                                        {--m|migration : Create a migration for the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'module' => ['Enter the name of the module', 'e.g., Auth/User'],
        ];
    }

    /**
     * Execute the console command.
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $path = $this->getModulePath($this->argument('module'));
        $name = $this->getModuleName($this->argument('module'));

        if ($this->files->exists(app_path("Modules/" . $path))) {
            $this->components->error("Module already exists.");
            return;
        }

        if ($this->option('migration')) {
            $this->createMigration($name);
        } else {
            if ($this->confirm('Do you want to create a migration for the module?')) {
                $this->createMigration($name);
            }
        }

        if ($this->option('model')) {
            $this->createModel($name, $path);
        } else {
            if ($this->confirm('Do you want to create a model for the module?')) {
                $this->createModel($name, $path);
            }
        }

        $this->createController($name, $path);
        $this->makeRoutes($name, $path);
    }

    private function getModulePath($argument): string
    {
        return Str::singular(ucwords($argument, "/"));
    }

    private function getModuleName($argument): array
    {
        $path = Str::singular(ucwords($argument, "/"));
        return explode("/", $path);
    }

    private function createModel($name, $path): void
    {
        $this->call('make:model', [
            'name' => "App\\Modules\\" . $path . "\\Models\\" .  array_pop($name)
        ]);
    }

    private function createMigration($name): void
    {
        $migrationName = Str::snake(Str::plural(array_pop($name)));
        $this->call('make:migration', [
            'name' => "create_{$migrationName}_table",
            '--create' => $migrationName,
        ]);
    }

    private function createController($name, $path): void
    {
        $this->call('make:controller', [
            'name' => "App\\Modules\\" . $path. "\\Controllers\\". array_pop($name). "Controller",
            '--resource' => true,
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    private function makeRoutes($name, $path): void
    {
        $routesPath = app_path("Modules/" . $path. "/Routes/api.php");
        if ($this->files->exists($routesPath)) {
            $this->components->error("Routes already exists.");
            return;
        }

        $stub = $this->files->get(app_path('../resources/stubs/routes.api.stub'));
        $stub = str_replace([
                'JummyRoutePrefix',
            ], [
                lcfirst(array_pop($name)),
            ],
            $stub
        );
        $this->files->ensureDirectoryExists(app_path("Modules/" . $path. "/Routes"));
        if ($this->files->put($routesPath, $stub)) {
            $this->components->info("Routes [app/Modules/" . $path. "/Routes/api.php" . "] created successfully.");
        }
    }
}
