<?php
namespace Bucorel\F2\Core;

use Exception;

class ApiClient {
    private $baseUrl;
    private $username;
    private $password;

    public function __construct(string $baseUrl, string $username, string $password) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Send a request to the backend.
     *
     * @param string $endpoint
     * @param string $method
     * @param array  $params
     * @param string $contentType  'form' | 'json'
     */
    public function request(
        string $endpoint,
        string $method = 'GET',
        array $params = [],
        string $contentType = 'form' // default keeps old behavior
    ) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $method = strtoupper($method);

        $ch = curl_init();
        $headers = [];

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);

            if ($contentType === 'json') {
                $payload = json_encode($params);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($payload);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            } else {
                // Default: form-urlencoded
                $payload = http_build_query($params);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            }

        } else {
            // GET request
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => "{$this->username}:{$this->password}",
            CURLOPT_TIMEOUT        => 30,
        ]);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            throw new Exception("Connection Error: $error");
        }

		/*
        if ($httpCode >= 400) {
            throw new Exception("HTTP Error: $httpCode | Response: $response");
        }
		*/
		
        return $this->parseResponse($response);
    }

    /**
     * Validates the standard JSON format: _sta, _mes, _dat
     */
    private function parseResponse($rawResponse) {
        $data = json_decode($rawResponse, true);

        if ( json_last_error() !== JSON_ERROR_NONE || !isset( $data['_sta'] ) ) {
            throw new Exception("Invalid API Response Format: " . $rawResponse);
        }

        return $data;
    }
}

