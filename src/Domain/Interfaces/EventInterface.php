<?php

namespace Leadsales\GatewayBridge\Domain\Interfaces;

interface EventInterface
{
    public function publish();
    
    public function emmit();
}
