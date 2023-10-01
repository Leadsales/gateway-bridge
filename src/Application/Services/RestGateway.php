<?php

namespace Leadsales\GatewayBridge\Application\Services;

use Leadsales\GatewayBridge\Domain\Abstracts\HandlerGateway;
use Leadsales\GatewayBridge\Domain\Interfaces\RestInterface;

class RestGateway extends HandlerGateway implements RestInterface
{
    public function get(string $uri):mixed
    {
        $this->communicator->subscribe($uri);
        return $this->communicator->receive();
    }

    public function post(string $uri, array $data):mixed
    {
        $this->communicator->subscribe($uri);
        return $this->communicator->send($data);
    }
}
