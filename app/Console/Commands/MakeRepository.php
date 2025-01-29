<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    protected $files;
    public function __construct(Filesystem $file)
    {
        parent::__construct();
        $this->files = $file;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--interface}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an New Repository with an optional interface';

    protected function getStub($file){
        if($file == 'interface'){
            return __DIR__ . '/stubs/interface.stub';
        } elseif($file == 'repository'){
            return __DIR__ . '/stubs/repository.stub';
        } elseif($file == 'interfacerepo'){
            return __DIR__ . '/stubs/interfacerepository.stub';
        }
    }

    protected function buildInterClass($name){
        $stub = $this->files->get($this->getStub('interface'));

        return str_replace('{{ class }}', $name, $stub);
    }

    protected function buildRepoClass($name){
        $stub = $this->files->get($this->getStub('repository'));

        return str_replace('{{ class }}', $name, $stub);
    }

    protected function buildInterRepoClass($repo, $inter){
        $stub = $this->files->get($this->getStub('interfacerepo'));

        return str_replace(['{{ class }}', '{{ interface }}'], [$repo, $inter], $stub);
    }

    protected function makeDirectory($path){
        if(!$this->files->isDirectory(dirname($path))){
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $interface = $this->option('interface');

        $repositoryPath = app_path("Repositories/{$name}.php");

        if($this->files->exists($repositoryPath)){
            $this->error('Repository already exists');
            return;
        }
        $this->makeDirectory($repositoryPath);

        if($interface){
            $in_name = $name."Interface";
            $interfacePath = app_path("Repositories/Interfaces/{$in_name}.php");
            if($this->files->exists($interfacePath)){
                $this->error('Interface already exists');
                return;
            }
            $this->makeDirectory($interfacePath);

            $this->files->put($repositoryPath, $this->buildInterRepoClass($name, $in_name));
            $this->info("Repository created successfully");
            $this->files->put($interfacePath, $this->buildInterClass($in_name));
            $this->info("Interface created successfully");
        } else {
            $this->files->put($repositoryPath, $this->buildRepoClass($name));
            $this->info("Repository created successfully");
        }
    }
}
