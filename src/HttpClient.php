<?php

namespace Symbolist;

use Symbolist\Exceptions\ApiException;

class HttpClient
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $baseUrl;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.hetrixtools.com/v3/')
    {
        if ($apiKey === '') {
            throw new \InvalidArgumentException('API key must not be empty.');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * @param array<string,mixed>      $query
     * @param array<string,mixed>|null $body
     *
     * @return array<mixed>
     * @throws ApiException
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        $query = array_filter($query, function ($value) {
            return $value !== null;
        });
        $url = $this->baseUrl . ltrim($path, '/');
        if ($query) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $handle = curl_init($url);
        if ($handle === false) {
            throw new ApiException('Failed to initialize HTTP request.');
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($body !== null) {
            $json = json_encode($body);
            if ($json === false) {
                curl_close($handle);
                throw new ApiException('Failed to encode request body: ' . json_last_error_msg());
            }
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = $json;
        }

        curl_setopt_array($handle, $options);
        $response = curl_exec($handle);
        $curlError = curl_error($handle);
        $statusCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($response === false) {
            throw new ApiException('HTTP request failed: ' . $curlError, $statusCode);
        }

        $decoded = $response === '' ? [] : json_decode($response, true);
        if ($response !== '' && $decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Failed to decode JSON response: ' . json_last_error_msg(), $statusCode, $response);
        }
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ApiException('HetrixTools API returned an error response.', $statusCode, $decoded);
        }

        return $decoded === null ? [] : $decoded;
    }
}
