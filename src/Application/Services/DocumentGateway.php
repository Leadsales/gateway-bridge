<?php

namespace Leadsales\GatewayBridge\Application\Services;

use Leadsales\GatewayBridge\Domain\Abstracts\HandlerGateway;

class DocumentGateway extends HandlerGateway
{
    
    public function get($fullPath):mixed
    {
        preg_match("#^/?([^/]+)(.*)#", $fullPath, $matches);
        $collection =  $matches[1] ?? '';
        $path = ltrim($matches[2] ?? '', '/');

        $this->communicator->subscribe($collection);
        return $this->communicator->receive($path);
    }
}