<?php

namespace Gateway\Domain\Interfaces;

interface RestInterface
{
    public function getUri(array $params, string $endpoint): string;
    
    public function get(string $uri):mixed;
    
    public function post(string $uri, array $data):mixed;
}
