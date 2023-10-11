<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use Exception;
use InvalidArgumentException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;

class FirebaseClientCommunicator implements GatewayInterface
{
    protected $auth;

    public function __construct($project = null)
    {
        $project ??= Firebase::getDefaultProject();
        $this->auth = Firebase::project($project)->auth();
    }

    public function connect(): bool
    {
        return true;
    }

    public function send(array $data): mixed
    {
        extract($data);
        return $this->auth->createUserWithEmailAndPassword($email, $password);
    }

    public function receive(string $userId=null, string $email=null): mixed
    {
        if (!$userId && !$email) {
            throw new InvalidArgumentException('You must provide at least one parameter (userId or email).');
        }

        if ($userId) {
            return $this->auth->getUser($userId);
        }
    
        if ($email) {
            return $this->auth->getUserByEmail($email);
        }
    
        return null;
    }
    public function disconnect(): bool
    {
        return true;
    }

    public function subscribe(string $value)
    {
        throw new Exception(message: "Subscribe method is not supported for Firebase.");
    }

    public function unsubscribe(string $value)
    {
        throw new Exception(message: "Unsubscribe method is not supported for Firebase.");
    }

}
