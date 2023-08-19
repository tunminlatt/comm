<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class MakeHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create helper directory inside app';

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
        $extension = 'php';
        $folderpath = 'Helpers';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Helpers;

class '. $name .'
{
    public function sample () {
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Helper already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Helper created successfully.</>');
        }
    }
}