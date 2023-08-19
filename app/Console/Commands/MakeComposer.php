<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class MakeComposer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:composer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create composer directory inside app';

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
        $folderpath = 'Http/View/Composers';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class '. $name .'
{
    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $view->with(\'count\', 1);
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Composer already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Composer created successfully.</>');
        }
    }
}
