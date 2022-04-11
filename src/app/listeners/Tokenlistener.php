<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;

class Tokenlistener extends injectable
{
    public function refreshToken(Event $event, \App\Components\ApiComponent $application)
    {
        $application->getRefreshedToken();
    }
}
