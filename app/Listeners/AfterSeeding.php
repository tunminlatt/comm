<?php

namespace App\Listeners;

use App\Events\SeedingEnded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Schema;

class AfterSeeding
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
     * @param  SeedingEnded  $event
     * @return void
     */
    public function handle(SeedingEnded $event)
    {
        // make sure no problem happens in deleting relationship rows
        Schema::enableForeignKeyConstraints();
    }
}
