# Cecula API Client
Cecula API Client is a simple client for connecting your PHP application to Cecula Messaging platform to send SMS via A2P channel or your hosted sim.

## How to Use
Import Cecula API Client to your application using composer.

```
composer require cecula/messaging-api
```
Once the library is imported to your application ensure all dependencies are installed by running
```
composer install
```
and you're ready to go.

### Get Your Cecula API Key
To get your Cecula API Key, login to the [Cecula Messaging Platform](https://app.cecula.com). If you do not already have an account you can create one right now.

Once you've logged in to your account, navigate to the Settings menu and that should be the first thing you see. Copy the Live or Sandbox API Key.

### Test the Library

Having retrieved your API key, you are now ready to test run the library.

#### In a Procedural PHP Script

```
<?php

use Cecula\MessagingApi\Messaging;

require_once __DIR__.'/vendor/autoload.php';

$messagingClient = new Messaging([
    'apiKey' => 'paste-your-api-key-here'
]);

echo $messagingClient->getBalance();

$smsParams = [
    'sender' => 'SENDER', // Note: this has to be pre-registered and approved
    'recipients' => ['2348XXXXXXXXX'],
    'text' => 'Happy for another day',
];

echo $messagingClient->sendSms($smsParams);
```

#### Inside a Class File 
What you'll likely be working with if using a framework like Laravel

```
<?php

use Cecula\MessagingApi\Messaging;

class ClassName
{
    public function doSomething()
    {
        $messagingClient = new Messaging([
            'apiKey' => 'paste-your-api-key-here'
        ]);

        echo $messagingClient->getBalance();

        $smsParams = [
            'sender' => 'SENDER', // Note: this has to be pre-registered and approved
            'recipients' => ['2348XXXXXXXXX'],
            'text' => 'Happy for another day',
        ];

        echo $messagingClient->sendSms($smsParams);
    }
}
```

You get it! Now adapt this and build messaging into your app.

If you get stuck, push a mail to lab@cecula.com - we'll get you back on dry codeland.

Cheers!!!