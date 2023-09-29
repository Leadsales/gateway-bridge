<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use Exception;
use GuzzleHttp\Client;
use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;

class GuzzleClientCommunicator implements GatewayInterface
{
    protected $client;
    protected $baseUrl;

    public function connect(): bool
    {
        // En API REST, no hay una "conexión". Por lo tanto, simplemente devolvemos true.
        return true;
    }

    public function send(array $data)
    {
        // Asumiendo que 'send' hace un POST a la API.
        $response = $this->client->post('/', [
            'json' => $data
        ]);
        return json_decode($response->getBody(), true);
    }

    public function receive()
    {
        // Asumiendo que 'receive' hace un GET a la API.
        $response = $this->client->get('/');
        return json_decode($response->getBody(), true);
    }

    public function disconnect(): bool
    {
        // En API REST, no hay una "desconexión". Por lo tanto, simplemente devolvemos true.
        return true;
    }

    public function subscribe(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    public function unsubscribe(string $topic)
    {
        throw new Exception(message:"Unsubscribe method is not supported for REST.");
    }
}