<?php

namespace App\Listeners;

use App\Events\NotificacionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionEventListener
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
     * @param  NotificacionEvent  $event
     * @return void
     */
    public function handle(NotificacionEvent $event)
    {
        //
    }
}
