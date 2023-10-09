<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQClientCommunicator implements GatewayInterface
{
    protected $connection;
    protected $channel;
    protected string $topic;

    public function __construct($host = null, $port = null, $user = null, $password = null, $vhost = '/', $topic = null)
    {
      $this->connection = new AMQPStreamConnection(
        $host ?? env('RMQ_HOST'),
        $port ?? env('RMQ_PORT'),
        $user ?? env('RMQ_USERNAME'),
        $password ?? env('RMQ_PASSWORD'),
        $vhost ?? env('RMQ_VHOST')
      );
        $this->channel = $this->connection->channel();
        $this->topic = $topic ?? 'default_queue';
    }

    public function connect(): bool
    {
        // La conexión ya se establecio en el constructor. Por lo tanto, solo verificaríamos si la conexión está activa.
        return $this->connection->isConnected();
    }

    public function send(array $data): mixed
    {
        // Por simplicidad, enviaremos mensajes a una cola predeterminada.
        $topic = $this->topic;

        $messageBody = json_encode($data);
        $message = new AMQPMessage($messageBody);

        $this->channel->queue_declare($topic, false, true, false, false);
        $this->channel->basic_publish($message, '', $topic);

        return true;
    }

    public function receive(): mixed
    {
        $topic = $this->topic;
        $this->channel->queue_declare($topic, false, true, false, false);

        $message = $this->channel->basic_get($topic);

        return json_decode($message->body, true);
    }

    public function disconnect(): bool
    {
        $this->channel->close();
        $this->connection->close();

        return !$this->connection->isConnected();
    }

    public function subscribe(string $topic)
    {
        // En RabbitMQ, los "topics" se refieren a rutas de binding en exchanges.
        // Por simplicidad, este método podría ser usado para consumir mensajes de una cola específica.
        $this->channel->queue_declare($topic, false, true, false, false);

        $this->channel->basic_consume($topic, '', false, true, false, false, function ($message) {
            return 'Received message: ' . $message->body . PHP_EOL;
        });

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function unsubscribe(string $topic)
    {
        // En realidad, no hay un "unsubscribe" directo en RabbitMQ como tal.
        // Pero podrías implementar lógica para dejar de consumir mensajes de una cola o eliminar la cola.
        // Por simplicidad, aquí solo eliminaremos la cola.
        $this->channel->queue_delete($topic);
    }
}
