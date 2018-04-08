<?php

namespace Railken\Laravel\App\Commands;

use Illuminate\Console\Command;

use Railken\Laravel\App\Generator;

class Generate extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'railken:make:src {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate all basics files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucfirst($this->argument('name'));

        $path = base_path("src/{$name}/");

        try {
            $gn = new Generator($path);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $gn->put("Http/routes.php", "Http/routes.php", [
            'URL' => strtolower($name)
        ]);

        $gn->put("Package.php", "Package.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Resources/views/welcome.blade.php", "Resources/views/welcome.blade.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Resources/public/assets/welcome/main.css", "Resources/public/assets/welcome/main.css");

        $gn->put("Providers/AppServiceProvider.php", "Providers/AppServiceProvider.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Providers/RouteServiceProvider.php", "Providers/RouteServiceProvider.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Http/Controllers/Controller.php", "Http/Controllers/Controller.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Http/Controllers/WelcomeController.php", "Http/Controllers/WelcomeController.php", [
            'NAMESPACE' => $name
        ]);

        $gn->put("Console/Commands/Welcome.php", "Console/Commands/Welcome.php", [
            'NAMESPACE' => $name,
            'NAME' => strtolower($name)
        ]);

        $this->info("\n".$name." generated");
    }
}
