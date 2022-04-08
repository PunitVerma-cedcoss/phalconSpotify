<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;

class Authlistener extends injectable
{
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        
    }
}
