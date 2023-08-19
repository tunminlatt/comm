<?php

namespace App\Console\Commands;

use App\Helpers\Image;
use App\Models\Programme;
use Illuminate\Console\Command;

class GenerateMediaRunTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:media-runtime {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Media Run Time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        parent::__construct();
        $this->image = $image;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->argument('count');
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        for ($i=1; $i <= $count ; $i++) {
            $programme = Programme::whereIn('type', ['audio', 'video'])
                            ->where('is_generated', 0)->first();
            if($programme) {
                $env = config('general.env');
                $id = $programme->id;
                $type = $programme->type;

                $rawDuration = $this->image->getDuration($this->image->getByApi('programmes/'.
                $id . '/' . $type, [], false, true));
                $duration = trim($rawDuration);

                $programme->update(['duration' => $duration, 'is_generated' => 1]);
            }

            $progressBar->advance();
        }
        $progressBar->finish();
        echo ' Run Times are generated!';
    }
}
