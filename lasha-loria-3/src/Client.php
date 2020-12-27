<?php

namespace StephaneCoinon\Mailtrap;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use StephaneCoinon\Mailtrap\Exceptions\MailtrapException;

class Client
{

    protected $apiToken;


    public $http;

    protected $errors = [];


    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
        $this->http = new HttpClient([
            'base_uri' => 'https://mailtrap.io/',
        ]);
    }


    public function request($method, $uri, $parameters = [], $headers = [])
    {
        $this->errors = [];

        $headers = [
            'Api-Token' => $this->apiToken,
        ];

        try {
            $response = $this->http->request($method, $uri, [
                'query' => $parameters,
                'headers' => $headers,
            ]);
        } catch (RequestException $guzzleException) {
            $mailtrapException = MailtrapException::create($guzzleException);
            $this->setErrors($mailtrapException);
            return null;
        }
        $body = $response->getBody()->getContents();
        $json = json_decode($body);
        return (json_last_error() === JSON_ERROR_NONE) ? $json : (string)$body;
    }

    public function get($uri, $parameters = [], $headers = [])
    {
        return $this->request('GET', $uri, $parameters, $headers);
    }


    public function patch($uri, $parameters = [], $headers = [])
    {
        return $this->request('PATCH', $uri, $parameters, $headers);
    }


    protected function setErrors(MailtrapException $exception)
    {
        $this->errors[] = (Object) [
            'status' => $exception->status,
            'message' => $exception->error,
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
