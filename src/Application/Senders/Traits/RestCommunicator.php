<?php

namespace Gateway\Application\Senders\Traits;

trait RestCommunicator
{
    public function getUri(array $params, string $endpoint): string
    {
        $url = config("gateway.$this->protocol.$endpoint");
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
    
    public function get(string $uri)
    {
        $this->communicator->subscribe($uri);
        return $this->communicator->receive();
    }
    public function post(string $uri, array $data)
    {
        $this->communicator->subscribe($uri);
        return $this->communicator->send($data);
    }
}
