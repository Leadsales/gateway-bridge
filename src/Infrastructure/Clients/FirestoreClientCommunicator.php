<?php

namespace Leadsales\GatewayBridge\Infrastructure\Clients;

use App\Exceptions\LeadSalesException;
use Exception;
use Google\Cloud\Firestore\CollectionReference;
use Google\Cloud\Firestore\DocumentReference;
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

    public function send(array $data = [], string $path = ''): mixed
    {
        [$cleanPath, $queryParams] = $this->splitPathAndQuery($path);
        $segments = array_filter(explode('/', $cleanPath));
    
        if (!$this->mainCollection) {
            throw new Exception('No collection has been subscribed to.');
        }
    
        $collectionOrDocument = $this->mainCollection;

        foreach ($segments as $segment) {
            if ($collectionOrDocument instanceof CollectionReference) {
                $collectionOrDocument = $collectionOrDocument->document($segment);
            } else {
                $collectionOrDocument = $collectionOrDocument->collection($segment);
            }
        }
    
        if ($collectionOrDocument instanceof DocumentReference) {
            $collectionOrDocument->set($data, ['merge' => true]);
    
            return $collectionOrDocument->snapshot()->data();
        } else {
            throw new Exception('The path provided does not lead to a document.');
        }
    }

    public function receive(string $path = '', int $limit = null, string $lastID = null): mixed
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
            $query = $collectionOrDocument;
    
            $orderByField = '__name__';
    
            if (!empty($queryParams)) {
                foreach ($queryParams as $key => $value) {
                    if(isset($value['like'])){
                        $val = $value['like'];
                        $query = $query->where($key, '>=', $val)->where($key, '<=', $val . '\uf8ff');
                        $orderByField = $key;
                    } else {
                        if($value === 'true') {
                            $value = true;
                        } elseif($value === 'false') {
                            $value = false;
                        } elseif($value === 'null') {
                            $value = null;
                        } elseif (is_numeric($value) && (int)$value == $value) {
                            $value = (int)$value;
                        }
                        $query = $query->where($key, '=', $value);
                    }
                }
            }
    
            $query = $query->orderBy($orderByField);
    
            $startAfterDocument = null;
            if ($lastID !== null) {
                $startAfterDocument = $collectionOrDocument->document($lastID)->snapshot();
            }
    
            if ($limit !== null) {
                $query = $query->limit($limit);
            }
    
            if ($startAfterDocument) {
                $query = $query->startAfter($startAfterDocument);
            }
    
            $documents = $query->documents();
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

    public function delete(string $path = ''): mixed
    {
        [$cleanPath, $queryParams] = $this->splitPathAndQuery($path);
        $segments = array_filter(explode('/', $cleanPath));
    
        if (!$this->mainCollection) {
            throw new Exception('No collection has been subscribed to.');
        }
    
        $collectionOrDocument = $this->mainCollection;

        foreach ($segments as $segment) {
            if ($collectionOrDocument instanceof CollectionReference) {
                $collectionOrDocument = $collectionOrDocument->document($segment);
            } else {
                $collectionOrDocument = $collectionOrDocument->collection($segment);
            }
        }

        if ($collectionOrDocument instanceof DocumentReference) {
            $collectionOrDocument->delete();
        } else {
            throw new Exception('The path provided does not lead to a document.');
        }

        if (!$this->mainCollection) {
            throw new Exception('No collection has been subscribed to.');
        }

        return ['message' => 'Document successfully deleted', 'documentId' => $segment];
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
            $parts['query'] = str_replace('+', '%2B', $parts['query']);
            parse_str($parts['query'], $queryParams);
        }

        return [$cleanPath, $queryParams];
    }
}
