<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

use App\Models\Programme;

use App\Repositories\ProgrammeRepository;

class ProgrammeSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'programme:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Every Minute Programme Approve by schedule time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProgrammeRepository $programmeRepository)
    {
        $this->programmeRepository = $programmeRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $programmes = Programme::whereNotNull('schedule')->get();
        $today = \Carbon\Carbon::now('Asia/Rangoon')->format('Y-m-d H:i');

        foreach($programmes as $programme){
            $schedule = \Carbon\Carbon::parse($programme->schedule, 'Asia/Rangoon')->format('Y-m-d H:i');

            if($today == $schedule || $today > $schedule){
                DB::beginTransaction();
                try {
                    $id = $programme->id;
                    $payload = ['state_id' => 2];

                    $this->programmeRepository->update($id, $payload, true);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                }
            }
        }
    }
}
