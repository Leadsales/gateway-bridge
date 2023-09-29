<?php

namespace Gateway\Infrastructure\Communicators;

use App\Exceptions\LeadSalesException;
use GuzzleHttp\Client;
use Gateway\Domain\Interfaces\CommunicatorInterface;

class GuzzleClientCommunicator implements CommunicatorInterface
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
        throw new LeadSalesException(message:"Unsubscribe method is not supported for REST.");
    }
}