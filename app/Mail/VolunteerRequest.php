<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($volunteer, $station)
    {
        $this->volunteer = $volunteer;
        $this->station = $station;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.VolunteerRequests.request')
        ->with(['volunteer' => $this->volunteer, 'station' => $this->station]);
    }
}
