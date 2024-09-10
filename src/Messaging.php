<?php

namespace Cecula\MessagingApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Messaging
{
    protected $apiKey;
    protected $client;
    protected $header;
    protected $baseUrl = 'https://app.cecula.com/api';
    protected $logger;

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

        // Initialize logger
        $this->logger = new Logger('cecula_messaging_api');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/logs/messaging.log', Logger::DEBUG));
    }

    /**
     * Send an SMS via the A2P channel using the Cecula platform.
     *
     * @param array $data An associative array containing:
     *   - 'recipients' (string|array): The phone number(s) of the recipients.
     *   - 'broadcastTime' (string|null): The time to broadcast the SMS (optional).
     *   - 'text' (string): The message content.
     *   - 'sender' (string): The sender ID.
     *   - 'flash' (int|null): Set to 1 for flash SMS (optional, default is 0).
     *
     * @return string JSON-encoded response containing the status and data or error message.
     */
    public function sendSms(array $data): string
    {
        $endpoint = 'sms/a2p/send';
        $params = [
            'recipients' => $data['recipients'],
            'broadcastTime' => $data['broadcastTime'] ?? null,
            'text' => $data['text'],
            'sender' => $data['sender'],
            'flash' => $data['flash'] ?? 0,
        ];

        return $this->postRequest($endpoint, $params);
    }

    /**
     * Fetch the SMS balance from the Cecula platform.
     *
     * @return string JSON-encoded response containing the status and data or error message.
     */
    public function getBalance(): string
    {
        $endpoint = 'sms/check-balance';
        return $this->getRequest($endpoint);
    }

    /**
     * Send a templated SMS via the A2P channel using the Cecula platform.
     *
     * @param array $data An associative array containing:
     *   - 'template' (string): The name or ID of the SMS template.
     *   - 'recipients' (string|array): The phone number(s) of the recipients.
     *   - 'sender' (string): The sender ID.
     *   - 'flash' (int|null): Set to 1 for flash SMS (optional).
     *
     * @return string JSON-encoded response containing the status and data or error message.
     */
    public function sendTemplateSms(array $data): string
    {
        $endpoint = 'sms/a2p/template/send';
        $params = [
            'template' => $data['template'],
            'recipients' => $data['recipients'],
            'sender' => $data['sender'],
            'flash' => $data['flash'] ?? 0,
        ];

        return $this->postRequest($endpoint, $params);
    }

    /**
     * Retrieve the list of sender names (identities) from the Cecula platform.
     *
     * @return string JSON-encoded response containing the status and the list of sender names or error message.
     */
    public function getSenderNames(): string
    {
        $endpoint = 'sms/identities';
        return $this->getRequest($endpoint);
    }

    /**
     * Centralized handler for sending POST requests.
     *
     * @param string $endpoint The API endpoint.
     * @param array $params The request parameters to send in the JSON body.
     *
     * @return string JSON-encoded response containing the status and data or error message.
     */
    private function postRequest(string $endpoint, array $params): string
    {
        try {
            $response = $this->client->post(sprintf('%s/%s', $this->baseUrl, $endpoint), [
                'headers' => $this->header['headers'],
                'json' => $params
            ]);

            // Log successful response
            $this->logger->info('POST request successful', [
                'endpoint' => $endpoint,
                'response_status' => $response->getStatusCode(),
                'response_data' => json_decode($response->getBody(), true)
            ]);

            return $this->processResponse($response);
        } catch (RequestException $exception) {

            // Log the error
            $this->logger->error('POST request failed', [
                'endpoint' => $endpoint,
                'error_code' => $exception->getCode(),
                'error_message' => $exception->getMessage()
            ]);

            return $this->processException($exception);
        }
    }

    /**
     * Centralized handler for sending GET requests.
     *
     * @param string $endpoint The API endpoint.
     *
     * @return string JSON-encoded response containing the status and data or error message.
     */
    private function getRequest(string $endpoint): string
    {
        try {
            $response = $this->client->get(sprintf('%s/%s', $this->baseUrl, $endpoint), $this->header);

            // Log successful response
            $this->logger->info('GET request successful', [
                'endpoint' => $endpoint,
                'response_status' => $response->getStatusCode(),
                'response_data' => json_decode($response->getBody(), true)
            ]);

            return $this->processResponse($response);
        } catch (RequestException $exception) {

            // Log the error
            $this->logger->error('GET request failed', [
                'endpoint' => $endpoint,
                'error_code' => $exception->getCode(),
                'error_message' => $exception->getMessage()
            ]);
            
            return $this->processException($exception);
        }
    }

    /**
     * Process the successful response from the API.
     *
     * @param \Psr\Http\Message\ResponseInterface $response The response object from Guzzle.
     *
     * @return string JSON-encoded response containing status and data.
     */
    private function processResponse($response): string
    {
        return json_encode([
            'status' => $response->getStatusCode(),
            'data' => json_decode($response->getBody(), true),
        ]);
    }

    /**
     * Process the exception thrown by Guzzle during the API call.
     *
     * @param \GuzzleHttp\Exception\RequestException $exception The caught exception.
     *
     * @return string JSON-encoded response containing status and error message.
     */
    private function processException(RequestException $exception): string
    {
        return json_encode([
            'status' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]);
    }
}
