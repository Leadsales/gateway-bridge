<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use App\Exceptions\LeadSalesException;
use Exception;
use Google\Cloud\Firestore\CollectionReference;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;

class FirestoreClientCommunicator implements GatewayInterface
{
    protected $database;
    protected $mainCollection;

    public function __construct($project = null)
    {
        try {
            $project ??= Firebase::getDefaultProject();
            $firestore = Firebase::project($project)->firestore();
        } catch (\Exception $e) {
            throw new LeadSalesException(
                message: $e->getMessage(),
                code:400
            );
        }
        
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

    public function receive(string $path = '', int $limit = null, string $lastDocumentId = null): mixed
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

            if ($limit !== null) {
                $collectionOrDocument = $collectionOrDocument->limit($limit);
            }
    
            // Ordena la colección por ID del documento
            $collectionOrDocument = $collectionOrDocument->orderBy('id');
    
            // Si se proporciona lastDocumentId, paginamos después de ese documento
            if ($lastDocumentId !== null) {
                $lastDocument = $collectionOrDocument->document($lastDocumentId)->snapshot();
                $collectionOrDocument = $collectionOrDocument->startAfter($lastDocument);
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
