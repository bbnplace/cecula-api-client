<?php

namespace Cecula\MessagingApi;

use GuzzleHttp\Client;

class Messaging
{
    protected $apiKey;
    protected $client;
    protected $header;
    protected $baseUrl = 'https://app.cecula.com/api';

    public function __construct(array $config)
    {
        $this->apiKey = $config['apiKey'];
        $this->client = new Client();
        $this->header = [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->apiKey),
                'Accept'    => 'application/json',
            ]
        ];
    }

    public function sendSms(array $data)
    {
        $endpoint = 'sms/a2p/send';
        $params = [
            'recipients' => $data['recipients'],
            'broadcastTime' => $data['broadcastTime'] ?? null,
            'text' => $data['text'],
            'sender' => $data['sender'],
            'flash' => $data['flash'] ?? 0,
        ];

        $response = $this->client->post(sprintf('%s/%s', $this->baseUrl, $endpoint), [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->apiKey),
                'Accept'    => 'application/json',
            ],
            'json' => $params
        ]);
        return $response->getBody();
    }

    public function getBalance()
    {
        $endpoint = 'sms/check-balance';
        $response = $this->client->get(sprintf('%s/%s', $this->baseUrl, $endpoint), $this->header);
        return $response->getBody();
    }

    public function sendTemplateSms(array $data)
    {
        $endpoint = 'sms/a2p/template/send';
        $params = [
            'template' => $data['template'],
            'recipients' => $data['recipients'],
            'sender' => $data['sender'],
            'flash' => $data['flash']
        ];

        $response = $this->client->post(sprintf('%s/%s', $this->baseUrl, $endpoint), [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->apiKey),
                'Accept'    => 'application/json',
            ],
            'json' => $params
        ]);

        return $response->getBody();
    }
}