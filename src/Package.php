<?php 

namespace Railken\Laravel\App;

use File;

class Package
{

    /**
     * app
     *
     * @var app
     */
    protected $service_provider;

    /**
     * Base path package
     *
     * @var string
     */
    public $base_path;

    /**
     * Namespace
     *
     * @var string
     */
    public $name;

    /**
     * Construct
     *
     * @return this
     */
    public function __construct($service_provider, $base_path, $name)
    {
        $this->service_provider = $service_provider;
        $this->base_path = $base_path;
        $this->name = $name;
    }
    

    public function getServiceProvider()
    {
        return $this->service_provider;
    }

    /**
     * Bootstrap any application package
     *
     * @return void
     */
    public function boot()
    {
        $this->load();
    }

    /**
     * Register the package
     *
     * @return void
     */
    public function register()
    {
    }
    
    /**
     * Load contents of package
     *
     * @return void
     */
    public function load()
    {
        $this->loadServices();
        //$this->loadResources();
    }
    
    /**
     * Load files
     *
     * @return void
     */
    public function loadServices()
    {
        $this->loadFiles('Console/Commands', 'Console\\Commands\\', function($files, $classes) {
            $this->getServiceProvider()->commands($classes);
        });
    }


    /**
     * Load a file and elaborate
     *
     * @param string $directory
     * @param string $namespace
     * @param Closure $closure
     *
     * @return void
     */
    public function loadFile($directory, $namespace, $closure)
    {
        $files = $this->getFiles($directory);

        $classes = [];

        $files->map(function($file) use($closure, $namespace, &$classes, &$files){


            $namespace = str_replace(base_path()."/src", "", $file);
            $namespace = dirname($namespace);
            $namespace = str_replace("/", "\\", $namespace)."\\";


            if (!is_dir($file)) {

                $class = $this->getClassByBasename(basename($file), $namespace);
                $classes[] = $class;
                $files[] = $file;
            }
        });

        foreach ($classes as $class) {

            $closure($class);
        }

    }

    /**
     * Load a file and elaborate
     *
     * @param string $directory
     * @param string $namespace
     * @param Closure $closure
     *
     * @return void
     */
    public function loadFiles($directory, $namespace, $closure)
    {
        $files = [];
        $classes = [];

        $files = $this->getFiles($directory);


        $files->map(function($file) use($closure, $namespace, &$classes, &$files){


            $namespace = str_replace(base_path()."/src", "", $file);
            $namespace = dirname($namespace);
            $namespace = str_replace("/", "\\", $namespace)."\\";


            if (!is_dir($file)) {

                $class = $this->getClassByBasename(basename($file), $namespace);
                $classes[] = $class;
                $files[] = $file;
            }
        });

        if (!empty($classes))
            $closure($files, $classes);
    }

    /**
     * Load resources
     *
     * @return void
     */
    public function loadResources()
    {
        // $this->loadViews();
        // $this->loadPublic();
    }

    /**
     * Load views
     *
     * @return void
     */
    public function loadViews()
    {
        $package = $this->base_path."/Resources/views";


        $this->getServiceProvider()->loadViewsFrom($package, $this->name);
    }

    /**
     * Load public
     *
     * @return void
     */
    public function loadPublic()
    {
        $directory = base_path("public/src");

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0775, true);
        }

        $basic = $directory."/".$this->name;

        $package = $this->base_path."/Resources/public";

        $this->createLink($package, $basic);
    }

    /**
     * Create link
     *
     * @param string $form
     * @param string $to
     *
     * @return void
     */
    public function createLink($from, $to)
    {
        if (File::exists($from)) {
            if (!File::exists($to)) {
                try {
                    symlink($from, $to);
                } catch (\Exception $e) {
                    throw new \Exception("Cannot create symlink from $from to $to");
                }
            }
        }
    }

    /**
     * Get class by basename
     *
     * @return string
     */
    public function getClassByBasename($basename, $namespace = '')
    {
        $file = basename($basename,".php");
        $class = $namespace.$file;
        return $class;
    }

    /**
     * get files
     *
     * @return collection
     */
    public function getFiles($directory)
    {
        $path = $this->base_path."/{$directory}";

        if (!is_dir($path)) {
            return file_exists($path) ? collect([$path]) : collect();
        }

        try { 
            return collect(File::allFiles($path));
        } catch (\Exception $e) {
            return collect();
        }
    }
}
