<?php

namespace App\Listeners;

use App\Events\SeedingStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Schema;

class BeforeSeeding
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SeedingStarted  $event
     * @return void
     */
    public function handle(SeedingStarted $event)
    {
        // make sure no problem happens in deleting relationship rows
        Schema::disableForeignKeyConstraints();
    }
}
