<?php

namespace Leadsales\GatewayBridge\Application\Services;

use Leadsales\GatewayBridge\Domain\Abstracts\HandlerGateway;
use Leadsales\GatewayBridge\Domain\Interfaces\EventInterface;

class EventGateway extends HandlerGateway implements EventInterface
{
    public function publish()
    {
    }
    
    public function emmit()
    {
    }
}
