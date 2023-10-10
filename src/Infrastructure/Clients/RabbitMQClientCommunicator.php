<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use Exception;
use Illuminate\Support\Facades\Log;
use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQClientCommunicator implements GatewayInterface
{
    protected $connection;
    protected $channel;
    protected string $topic;
    protected $queueDeclare;

    public function connect($host = null, $port = null, $user = null, $password = null, $vhost = '/'): bool
    {
        try {
            $this->connection = new AMQPStreamConnection(
                $host ?? env('RMQ_HOST'),
                $port ?? env('RMQ_PORT'),
                $user ?? env('RMQ_USERNAME'),
                $password ?? env('RMQ_PASSWORD'),
                $vhost ?? env('RMQ_VHOST')
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

    public function send(array $data): mixed
    {
        $messageBody = json_encode($data);
        $message = new AMQPMessage($messageBody);

        $this->channel->basic_publish($message, '', $this->topic);

        return true;
    }

    public function receive(): mixed
    {
        $message = $this->channel->basic_get($this->topic);

        return json_decode($message->body, true);
    }

    public function disconnect(): bool
    {
        $this->channel->close();
        $this->connection->close();

        return !$this->connection->isConnected();
    }

    public function subscribe(string $topic = null)
    {
        $this->topic = $topic ?? 'default_queue';
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->topic, false, true, false, false);
    }

    public function unsubscribe(string $topic)
    {
        // En realidad, no hay un "unsubscribe" directo en RabbitMQ como tal.
        // Pero podrías implementar lógica para dejar de consumir mensajes de una cola o eliminar la cola.
        // Por simplicidad, aquí solo eliminaremos la cola.
        $this->channel->queue_delete($topic);
    }
}
