<?php

namespace Gateway\Application\Services;
use Gateway\Domain\Abstracts\HandlerGateway;
use Gateway\Domain\Interfaces\EventInterface;

class EventGateway extends HandlerGateway implements EventInterface
{
    public function publish()
    {
    }
    
    public function emmit()
    {
    }
}
