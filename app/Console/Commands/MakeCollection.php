<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class MakeCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:collection {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create collection directory inside app';

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
        $folderpath = 'Collections';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class '. $name .' extends Collection
{
    public function sample () {
        return $this->count();
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Collection already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Collection created successfully.</>');
        }
    }
}