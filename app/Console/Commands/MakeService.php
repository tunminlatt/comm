<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Storage;

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
    protected $description = 'Create service directory inside app';

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
        // prepare variables
        $storage = Storage::disk('app');
        $name = $this->argument('name');
        $singularFirstCapName = str_replace('Service', '', $name);
        $singularSmallName = strtolower($singularFirstCapName);
        $pluralSmall = Str::plural($singularSmallName);
        $extension = 'php';
        $folderpath = 'Services';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Services;

use App\Repositories\\'. $singularFirstCapName .'Repository;

class '. $name .'
{
    public function __construct(
        '. $singularFirstCapName .'Repository $'. $singularSmallName .'Repository
    ) {
        $this->'. $singularSmallName .'Repository = $'. $singularSmallName .'Repository;
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Service already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Service created successfully.</>');
        }
    }
}