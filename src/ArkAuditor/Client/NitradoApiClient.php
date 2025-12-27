<?php

declare(strict_types=1);

namespace App\ArkAuditor\Client;

use App\ArkAuditor\Exception\NitradoApiException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class NitradoApiClient
{
    private const BASE_URL = 'https://api.nitrado.net';
    private const TIMEOUT = 30;
    
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $apiToken,
    ) {}

    public function getServiceInfo(string $serviceId): array
    {
        return $this->request('GET', "/services/{$serviceId}/gameservers");
    }

    public function getPlayers(string $serviceId): array
    {
        $response = $this->request('GET', "/services/{$serviceId}/gameservers/games/players");
        return $response['players'] ?? [];
    }

    public function getServerStats(string $serviceId): array
    {
        return $this->request('GET', "/services/{$serviceId}/gameservers/stats");
    }

    public function getLogs(string $serviceId, int $hours = 24): array
    {
        $response = $this->request('GET', "/services/{$serviceId}/gameservers/games/logs", [
            'hours' => min(48, max(1, $hours)),
        ]);
        
        return $response['logs'] ?? [];
    }

    public function listFiles(string $serviceId, string $path = '/'): array
    {
        $response = $this->request('GET', "/services/{$serviceId}/gameservers/file_server/list", [
            'dir' => $path,
        ]);
        
        return $response['entries'] ?? [];
    }

    public function downloadFile(string $serviceId, string $filePath): string
    {
        $response = $this->request('GET', "/services/{$serviceId}/gameservers/file_server/download", [
            'file' => $filePath,
        ]);
        
        if (!isset($response['token'])) {
            throw new NitradoApiException('No download token received');
        }

        try {
            $downloadResponse = $this->httpClient->request('GET', $response['token']['url']);
            return $downloadResponse->getContent();
        } catch (TransportExceptionInterface $e) {
            throw new NitradoApiException("Failed to download file: {$e->getMessage()}", previous: $e);
        }
    }

    public function executeRconCommand(string $serviceId, string $command): array
    {
        return $this->request('POST', "/services/{$serviceId}/gameservers/games/rcon", [
            'command' => $command,
        ]);
    }

    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = self::BASE_URL . $endpoint;
        
        $options = [
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'Accept' => 'application/json',
            ],
            'timeout' => self::TIMEOUT,
        ];

        if ($method === 'GET' && !empty($params)) {
            $options['query'] = $params;
        } elseif (!empty($params)) {
            $options['json'] = $params;
        }

        try {
            $this->logger->debug('Nitrado API request', [
                'method' => $method,
                'endpoint' => $endpoint,
                'params' => $params,
            ]);

            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $content = $response->toArray();

            if ($statusCode >= 400) {
                throw new NitradoApiException(
                    "API error: {$content['message'] ?? 'Unknown error'}",
                    $statusCode
                );
            }

            return $content['data'] ?? $content;

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Nitrado API transport error', [
                'error' => $e->getMessage(),
                'endpoint' => $endpoint,
            ]);
            throw new NitradoApiException("Transport error: {$e->getMessage()}", previous: $e);
        } catch (\JsonException $e) {
            throw new NitradoApiException("Invalid JSON response: {$e->getMessage()}", previous: $e);
        }
    }
}
