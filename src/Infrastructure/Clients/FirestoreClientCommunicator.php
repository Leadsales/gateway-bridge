<?php

namespace Gateway\Infrastructure\Clients;

use Exception;
use Gateway\Domain\Interfaces\GatewayInterface;
use Google\Cloud\Firestore\CollectionReference;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirestoreClientCommunicator implements GatewayInterface
{
    protected $database;
    protected $mainCollection; 

    public function __construct($project = null)
    {
        $project ??= Firebase::getDefaultProject();
        $firestore = Firebase::project($project)->firestore();
        $this->database = $firestore->database();
    }

    public function connect(): bool
    {
        return true;
    }

    public function send(array $data): mixed
    {
        // Implementación para guardar data...
        return true;
    }

    public function receive(string $path = ''): mixed
    {
        // Divide el path y los query params
        [$cleanPath, $queryParams] = $this->splitPathAndQuery($path);
    
        $segments = array_filter(explode('/', $cleanPath));
        $collectionOrDocument = $this->mainCollection;
    
        foreach ($segments as $segment) {
            if ($collectionOrDocument instanceof CollectionReference) {
                $collectionOrDocument = $collectionOrDocument->document($segment);
            } else {
                $collectionOrDocument = $collectionOrDocument->collection($segment);
            }
        }
    
        if ($collectionOrDocument instanceof CollectionReference) {
            if (!empty($queryParams)) {
                foreach ($queryParams as $key => $value) {
                    $collectionOrDocument = $collectionOrDocument->where($key, '=', $value);
                }
            }
    
            $documents = $collectionOrDocument->documents();
            $results = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $results[] = $document->data();
                }
            }
            return $results;
        } else {
            $snapshot = $collectionOrDocument->snapshot();
            return $snapshot->data();
        }
    }
    public function disconnect(): bool
    {
        return true;
    }

    public function subscribe(string $collectionName)
    {
        $this->mainCollection = $this->database->collection($collectionName);
    }

    public function unsubscribe(string $project)
    {
        throw new Exception(message: "Unsubscribe method is not supported for Firestore.");
    }

    private function splitPathAndQuery(string $path): array
    {
        $parts = parse_url($path);
        $cleanPath = $parts['path'] ?? '';
        $queryParams = [];

        if (isset($parts['query'])) {
            parse_str($parts['query'], $queryParams);
        }

        return [$cleanPath, $queryParams];
    }
}
