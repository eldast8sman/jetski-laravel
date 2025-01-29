<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service Class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    protected function makeDirectory($path){
        if(!$this->files->isDirectory(dirname($path))){
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function getStub(){
        return __DIR__ . '/stubs/service.stub';
    }

    protected function getNameSpace(){
        return 'App\Services';
    }

    protected function getClassName($name){
        return $name;
    }

    protected function buildClass($name){
        $stub = $this->files->get($this->getStub());

        return str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$this->getNameSpace(), $this->getClassName($name)],
            $stub
        );
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path  = app_path('Services/'.$name.'.php');

        if($this->files->exists($path)){
            $this->error('Service already exists!');
            return;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->info('Service created successfully');
    }
}
