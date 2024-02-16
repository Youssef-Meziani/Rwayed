<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReCaptchaService
{
    private $httpClient;
    private $secretKey;

    public function __construct(HttpClientInterface $httpClient, string $secretKey)
    {
        $this->httpClient = $httpClient;
        $this->secretKey = $secretKey;
    }

    public function validate($token): bool
    {
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->secretKey,
                'response' => $token,
            ],
        ]);

        $responseData = $response->toArray();

//        dd($responseData,$token);

        return $responseData['success'] && $responseData['score'] >= 0.5;
    }
}