<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking;

use ElliotJReed\GFS\Tracking\Entity\ProofOfDelivery as ProofOfDeliveryEntity;
use ElliotJReed\GFS\Tracking\Exception\ServerError;
use ElliotJReed\GFS\Tracking\Exception\UnexpectedResponse;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class ProofOfDelivery extends Tracking
{
    /**
     * @throws Exception\ConsignmentNotFound
     * @throws Exception\InvalidApiAccessLevel
     * @throws Exception\MissingOrMalformedApiKey
     * @throws ServerError
     * @throws UnexpectedResponse
     */
    public function getProofOfDelivery(string $carrier, string $consNo): ?ProofOfDeliveryEntity
    {
        try {
            $response = $this->client->get($this->baseUri . '/connect/finder', [
                'query' => [
                    'carrier' => $carrier,
                    'consNo' => $consNo,
                ],
                'headers' => [
                    'X-API-KEY' => $this->apiKey
                ]
            ]);

            try {
                $data = \json_decode($response->getBody()->getContents(), true, 32, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                throw new UnexpectedResponse($this->formatError($exception), previous: $exception);
            }

            $response = $this->client->get($this->baseUri . $data[0]['consignment']['href'], [
                'headers' => [
                    'X-API-KEY' => $this->apiKey
                ]
            ]);
        } catch (RequestException $exception) {
            $this->handleRequestException($exception);
        } catch (ClientExceptionInterface $exception) {
            throw new ServerError($this->formatError($exception), previous: $exception);
        }

        try {
            $consignmentData = \json_decode($response->getBody()->getContents(), true, 32, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new UnexpectedResponse($this->formatError($exception), previous: $exception);
        }

        if (isset($consignmentData['pod'])) {
            return $this->requestProofOfDelivery($consignmentData['pod']['href']);
        }

        return null;
    }
}
