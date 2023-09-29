<?php

namespace Leadsales\GatewayBridge\Application\Services;

use Leadsales\GatewayBridge\Domain\Abstracts\HandlerGateway;
use Leadsales\GatewayBridge\Domain\Interfaces\RestInterface;

class RestGateway extends HandlerGateway implements RestInterface
{
    public function getUri(array $params, string $endpoint): string
    {
        $url = config("gateway.rest.$endpoint");
        if (!$url) {
            throw new \InvalidArgumentException('Endpoint not found in the configuration.');
        }
    
        return preg_replace_callback('/{{(\w+)}}/', function ($matches) use ($params) {
            $key = $matches[1];
            if (!isset($params[$key])) {
                throw new \InvalidArgumentException("The parameter {$key} was not provided.");
            }
            return $params[$key];
        }, $url);
    }
    
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
