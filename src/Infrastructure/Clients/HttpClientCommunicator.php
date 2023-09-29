<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use Exception;
use Illuminate\Support\Facades\Http;
use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;

class HttpClientCommunicator implements GatewayInterface
{
    protected string $baseUrl;

    public function connect(): bool
    {
        // En API REST, no hay una "conexión". Por lo tanto, simplemente devolvemos true.
        return true;
    }

    public function send(array $data):mixed
    {
        try {
            // Asumiendo que 'send' hace un POST a la API.
            $response = Http::post($this->baseUrl, $data);
        } catch (Exception $e) {
            throw new Exception(
                message:$e->getMessage(),
                code:500
            );
        }

        if(!$response->successful()){
            throw new Exception(
                code:$response->status()
            );
        }
        
        return $response->json();
    }

    public function receive():mixed
    {
        try {
            // Asumiendo que 'receive' hace un GET a la API.
            $response = Http::get($this->baseUrl);
        } catch (Exception $e) {
            throw new Exception(
                message:$e->getMessage(),
                code:500
            );
        }

        if(!$response->successful()){
            throw new Exception(
                code:$response->status()
            );
        }
        
        return $response->json();
    }

    public function disconnect(): bool
    {
        // En API REST, no hay una "desconexión". Por lo tanto, simplemente devolvemos true.
        return true;
    }

    public function subscribe(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function unsubscribe(string $topic)
    {
        throw new Exception(message:"Unsubscribe method is not supported for REST.");
    }
}
