<?php

namespace Leadsales\GatewayBridge\Domain\Interfaces;

interface RestInterface
{
    public function get(string $uri):mixed;
    
    public function post(string $uri, array $data):mixed;
}
