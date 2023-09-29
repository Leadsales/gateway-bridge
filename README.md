# LeadSales Gateway

> **Note:** This package provides unified management of various communication protocols, including a REST API, a Message Broker (such as Kafka or RabbitMQ) or Firestore. This simplifies interaction with these services in a straightforward and transparent manner, using consistent methods that enable working with these different protocols in a uniform and efficient way

## Use

#### Crear un archivo de configuración que deberá contener los endpoints o los parametros con los que interactuara el comunicador.

Path de ejemplo: `Infrastructure/Config/gateway.php`

**Donde:**

- host: env('HOST'): variable de entorno con el host del proveedor
- url_param: elemento dentro de la url variable
- path: otros elementos que conforman la url

> **Ejemplo de archivo de configuración:** En el ejemplo de codigo se define el protocolo de comunicación `api` para el proveedor `whatsapp_web` con dos endpoints `status` y `qr`, con sus respectivos endpoints, en donde `?workspaceUuid` es una variable dentro del endpoint, tambien se podria utilizar `{{workspaceUuid}}` o `:workspaceUuid:`. Solo de debe poder identificar cual(es) parte(s) del path en el endpoint son variables para su posterior reemplazo por el valor

```php
return [
    'rest'=> [
        'whatsapp_web' => [
            'status' => env('HOST_WHATSAPP_WEB').'/?workspaceUuid/status',
            'qr' => env('HOST_WHATSAPP_WEB').'/?workspaceUuid/qr',
        ],
    ],
];

```

#### Registrar en `.env` la ruta del archivo de configuración.

```shell
GATEWAY_PATH="{{package_name}}/Infrastructure/Config/gateway.php"
FIREBASE_PROJECT=
FIREBASE_TYPE=
FIREBASE_PROJECT_ID=
FIREBASE_PRIVATE_KEY_ID=
FIREBASE_PRIVATE_KEY=
FIREBASE_CLIENT_EMAIL=
FIREBASE_CLIENT_ID=
FIREBASE_AUTH_URI=
FIREBASE_TOKEN_URI=
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=
FIREBASE_CLIENT_X509_509=
FIREBASE_UNIVERSE_DOMAIN=
```

#### Crear un Sender ya que cada sender puede tener sus propias particularidades

```php
namespace Leads\Application\Senders;

use Gateway\Application\Senders\Traits\RestCommunicator;
use Gateway\Domain\Abstracts\Sender;
use Gateway\Domain\Interfaces\CommunicatorInterface;

class ApiSender extends Sender
{
    use RestCommunicator;

    public function __construct(CommunicatorInterface $communicator, $protocol='api')
    {
        parent::__construct($communicator);
        $this->protocol = $protocol;
    }
}
```

#### Implementar el Sender

```php
# Elegir el protocolo de comunicación
$communicator = new HttpClientCommunicator;

# Definir el Sender que se utilizará
$sender = new ApiSender($communicator);


$params = [
    'workspaceUuid' => 'deabeaf9-0cca-4b21-84df-b4554ffc0e05'
];
$endpoint = 'workspace_service.status';

# Obtiene la Url desde el archivo de configuración
$uri = $sender->getUri($params, $endpoint);

# Para un request tipo GET
$response = $sender->get($uri);

# para un request tipo POST
$response = $sender->post($uri, $data);

/*** Ejemplo de response
 array:4 [
  "code" => 201
  "message" => "Workspace Status found successfully"
  "data" => array:1 [
    "active" => true
  ]
  "success" => true
]
***/


```
