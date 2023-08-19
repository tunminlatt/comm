<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class MakeTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create trait directory inside app';

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
        $folderpath = 'Traits';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Traits;

trait '. $name .'
{
    public function sample () {
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Trait already exists!</>');
        } else {
            $this->line('<fg=green>Trait created successfully.</>');
        }
    }
}