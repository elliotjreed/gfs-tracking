<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking;

use ElliotJReed\GFS\Tracking\Entity\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Consignment
{
    private const BASE_URI = 'https://ecm.gfsdeliver.com';

    private ClientInterface $httpClient;
    private string $apiKey;

    /**
     * @param ClientInterface $httpClient Guzzle HTTP client instance.
     * @param string $apiKey API key for authentication.
     */
    public function __construct(ClientInterface $httpClient, string $apiKey, string $baseUri = self::BASE_URI)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    /**
     * Lookup a consignment using carrier and consignment number.
     *
     * @param string $carrier The carrier identifier.
     * @param string $consignmentNumber The consignment number.
     * @return Response[] Array of Response objects.
     *
     * @throws ApiException
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws NotFoundException
     * @throws ServerException
     */
    public function lookupConsignment(string $carrier, string $consignmentNumber): array
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URI . '/connect/finder', [
                'headers' => [
                    'X-API-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'carrier' => $carrier,
                    'consNo' => $consignmentNumber
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = \json_decode($body, true);

            if ($statusCode === 200) {
                $consignments = [];
                foreach ($data as $item) {
                    $consignments[] = Response::fromArray($item);
                }
                return $consignments;
            }

            $this->handleErrorResponse($statusCode, $data);
        } catch (GuzzleException $e) {
            throw new ApiException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }

        // Should not reach here
        throw new ApiException('Unexpected error occurred.');
    }

    private function handleErrorResponse(int $statusCode, ?array $data): void
    {
        $message = $data['message'] ?? 'An error occurred';
        $code = $data['code'] ?? $statusCode;

        switch ($statusCode) {
            case 401:
                throw new AuthenticationException($message, $code);
            case 403:
                throw new AuthorizationException($message, $code);
            case 404:
                throw new NotFoundException($message, $code);
            case 500:
                throw new ServerException($message, $code);
            default:
                throw new ApiException($message, $code);
        }
    }
}
