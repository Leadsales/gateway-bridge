# LeadSales Gateway

> **Note:** This package provides unified management of various communication protocols, including a REST API, a Message Broker (such as Kafka or RabbitMQ) or Firestore. This simplifies interaction with these services in a straightforward and transparent manner, using consistent methods that enable working with these different protocols in a uniform and efficient way

## Use

#### Copiar manualmente firebase.php en la carpeta config

#### Registrar el Paquete en Lumen: bootstrap/app.php

```shell
# leadsales/gateway-bridge
$app->configure('firebase');
$app->register(Kreait\Laravel\Firebase\ServiceProvider::class);
$app->register(\Leadsales\GatewayBridge\ServiceProvider::class);
```

#### Registrar en `.env` la ruta del archivo de configuración.

```shell
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

#### Implementación

```php
# Elegir el protocolo de comunicación
$communicator = new HttpClientCommunicator;

# Definir el Sender que se utilizará
$gateway = new RestGateway($communicator);

// Definir un endpoint
$uri = 'https://api.example.com/users/123';

# Para un request tipo GET
$response = $gateway->get($uri);

# para un request tipo POST
$response = $gateway->post($uri, $data);

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
