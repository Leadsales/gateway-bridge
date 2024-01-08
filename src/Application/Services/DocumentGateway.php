<?php

namespace Leadsales\GatewayBridge\Application\Services;

use Leadsales\GatewayBridge\Domain\Abstracts\HandlerGateway;

class DocumentGateway extends HandlerGateway
{
    public function get($fullPath, $limit=null, $lastDocumentId = null):mixed
    {
        preg_match("#^/?([^/]+)(.*)#", $fullPath, $matches);
        $collection =  $matches[1] ?? '';
        $path = ltrim($matches[2] ?? '', '/');

        $this->communicator->subscribe($collection);
        return $this->communicator->receive($path, $limit, $lastDocumentId);
    }

    public function set($fullPath, array $data):mixed
    {
        preg_match("#^/?([^/]+)(.*)#", $fullPath, $matches);
        $collection =  $matches[1] ?? '';
        $path = ltrim($matches[2] ?? '', '/');

        $this->communicator->subscribe($collection);
        return $this->communicator->send($data, $path);
    }

    public function del($fullPath): mixed
    {
        preg_match("#^/?([^/]+)(.*)#", $fullPath, $matches);
        $collection =  $matches[1] ?? '';
        $path = ltrim($matches[2] ?? '', '/');
        $this->communicator->subscribe($collection);
        return $this->communicator->delete($path);
    }
    
    public function setUser($email, $password):mixed
    {
        $data = [
            'email' => $email,
            'password' => $password
        ];
        return $this->communicator->send($data);
    }

}
