<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking;

use ElliotJReed\GFS\Tracking\Exception\UnexpectedResponse;
use GuzzleHttp\Exception\RequestException;

class TrackingEvents extends Tracking
{
    /**
     * @return \ElliotJReed\GFS\Tracking\Entity\TrackingEvent[]
     *
     * @throws Exception\ConsignmentNotFound
     * @throws Exception\InvalidApiAccessLevel
     * @throws Exception\MissingOrMalformedApiKey
     * @throws Exception\ServerError
     * @throws UnexpectedResponse
     */
    public function getTrackingEvents(string $carrier, string $consNo): array
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
        }

        try {
            $consignmentData = \json_decode($response->getBody()->getContents(), true, 32, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new UnexpectedResponse($this->formatError($exception), previous: $exception);
        }

        return $this->requestTrackingEvents($consignmentData['parcels']);
    }
}
