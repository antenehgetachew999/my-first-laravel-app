<?php

namespace App\EventListners;

use App\Events\ExampleEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExampleEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
      
    }

    /**
     * Handle the event.
     */
    public function handle(ExampleEvent $event): void
    {
        Log::debug("The user {$event->username} just performed {$event->action} ");
    }
}