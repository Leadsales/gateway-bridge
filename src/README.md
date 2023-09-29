# LeadSales Gateway Bridge

This package provides unified management of various communication protocols, including a REST API, a Message Broker (such as Kafka or RabbitMQ), or Firestore. It simplifies interaction with these services in a straightforward and transparent manner, using consistent methods that enable working with these different protocols in a uniform and efficient way.

## Características Principales

- **Protocol Unification:** The package allows for the unification and standardization of various communication protocols commonly used in microservices. These protocols include REST API, message brokers (such as Kafka or RabbitMQ), Firestore, and more.

- **Custom Configuration:** It allows defining custom configurations for each protocol and provider in a configuration file. This provides flexibility to adapt to the specific requirements of each service.

## Usage

### Creating a Configuration File.

Create a configuration file that should contain the endpoints or parameters with which the communicator will interact. You can place this file in a path like `Infrastructure/Config/gateway.php`.

**Where:**

- `host`: env('HOST'): Environment variable with the host of the provider.
- `url_param`: An element within the URL variable.
- `path`: Other elements that make up the URL.

> **Example of a configuration file:** In the code example below, the communication protocol `api` is defined for the provider `whatsapp_web` with two endpoints, `status` and `qr`, along with their respective endpoints. Here, `{{workspaceUuid}}` is a variable within the endpoint. You should be able to identify which part(s) of the path in the endpoint are variables for later replacement with a value.

```php
return [
    'api'=> [
        'whatsapp_web' => [
            'status' => env('HOST_WHATSAPP_WEB').'/?workspaceUuid/status',
            'qr' => env('HOST_WHATSAPP_WEB').'/?workspaceUuid/qr',
        ],
    ],
];
```

### Register the Configuration File in `.env`.

```shell
GATEWAY_PATH="{{package_name}}/Infrastructure/Config/gateway.php"
```

### Create a Sender

Create a sender class because each sender can have its particularities. Here's an example of an `ApiSender` class:

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

### Implement the Sender

Choose the communication protocol, define the sender you want to use, and implement it as shown below:

```php
# Choose the communication protocol
$communicator = new HttpClientCommunicator;

# Define the sender to be used
$sender = new ApiSender($communicator);

$params = [
    'workspaceUuid' => 'deabeaf9-0cca-4b21-84df-b4554ffc0e05'
];
$endpoint = 'workspace_service.status';

# Get the URL from the configuration file
$uri = $sender->getUri($params, $endpoint);

# For a GET request
$response = $sender->get($uri);

# For a POST request
$response = $sender->post($uri, $data);

/*** Example of response
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
