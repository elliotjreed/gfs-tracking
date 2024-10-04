<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking;

use ElliotJReed\GFS\Tracking\Entity\File;
use ElliotJReed\GFS\Tracking\Entity\Geo;
use ElliotJReed\GFS\Tracking\Entity\Location;
use ElliotJReed\GFS\Tracking\Entity\Parcel;
use ElliotJReed\GFS\Tracking\Entity\ProofOfDelivery;
use ElliotJReed\GFS\Tracking\Entity\TrackingEvent;
use ElliotJReed\GFS\Tracking\Exception\ConsignmentNotFound;
use ElliotJReed\GFS\Tracking\Exception\InvalidApiAccessLevel;
use ElliotJReed\GFS\Tracking\Exception\MissingOrMalformedApiKey;
use ElliotJReed\GFS\Tracking\Exception\ServerError;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

abstract class Tracking
{
    protected const BASE_URI = 'https://ecm.gfsdeliver.com';

    public function __construct(
        protected readonly ClientInterface $client,
        protected readonly string $apiKey,
        protected readonly string $baseUri = self::BASE_URI
    ) {
    }

    protected function requestProofOfDelivery(string $href): ?ProofOfDelivery
    {
        try {
            $response = $this->client->get($this->baseUri . $href, [
                'headers' => [
                    'X-API-KEY' => $this->apiKey
                ]
            ]);

            $podData = \json_decode($response->getBody()->getContents(), true, 32, \JSON_THROW_ON_ERROR);

            $images = [];
            foreach ($podData['images'] as $image) {
                if (isset($image['mimeType']) || isset($image['stream'])) {
                    $file = new File();

                    if (isset($image['mimeType'])) {
                        $file->setMimeType($image['mimeType']);
                    }

                    if (isset($image['stream'])) {
                        $file->setStream($image['stream']);
                    }

                    $images[] = $file;
                }
            }

            $location = (new Location())
                ->setLocationText($podData['location']['locationText']);

            if (isset($podData['location']['geo'])) {
                $location->setGeo((new Geo())
                    ->setLongitude($podData['location']['geo']['longitude'])
                    ->setLatitude($podData['location']['geo']['latitude']));
            }

            return (new ProofOfDelivery())
                ->setSignee($podData['signee'])
                ->setItemCount($podData['itemCount'])
                ->setDeliveryDateTime(new \DateTimeImmutable($podData['deliveryDateTime']))
                ->setDriverComment($podData['driverComment'])
                ->setLocation($location)
                ->setImages($images);
        } catch (\JsonException | RequestException $exception) {
        }

        return null;
    }

    protected function requestTrackingEvents($parcels): array
    {
        $trackingEvents = [];

        foreach ($parcels as $parcel) {
            $events = [];
            try {
                $response = $this->client->get($this->baseUri . $parcel . '/track', [
                    'headers' => [
                        'X-API-KEY' => $this->apiKey
                    ]
                ]);

                $contents = $response->getBody()->getContents();
                $parcelEventsData = \json_decode($contents, true, 32, \JSON_THROW_ON_ERROR);

                foreach ($parcelEventsData['events'] as $parcelEventsDatum) {
                    $event = (new TrackingEvent())
                        ->setText($parcelEventsDatum['text'])
                        ->setDateTime(new \DateTimeImmutable($parcelEventsDatum['dateTime']));

                    if (isset($parcelEventsDatum['location'])) {
                        $location = (new Location())->setLocationText($parcelEventsDatum['location']['locationText']);

                        if (isset($parcelEventsDatum['location']['geo'])) {
                            $location->setGeo((new Geo())
                                ->setLatitude($parcelEventsDatum['location']['geo']['latitude'])
                                ->setLongitude($parcelEventsDatum['location']['geo']['longitude']));
                        }

                        $event->setLocation($location);
                    }

                    $events[] = $event;
                }
            } catch (\JsonException | RequestException $exception) {
            }

            $trackingEvents[] = (new Parcel())->setTrackingEvents($events);
        }

        return $trackingEvents;
    }

    protected function formatError(\Throwable $exception): string
    {
        if ($exception instanceof RequestException) {
            $response = $exception->getResponse();
            try {
                $error = \json_decode($response->getBody()->getContents(), true, 32, \JSON_THROW_ON_ERROR);

                if (isset($error['message'])) {
                    return $error['message'] . ' (' . $error['code'] . '): ' . $error['details'];
                }
            } catch (\JsonException $e) {
                return 'API response HTTP status code: ' . $response->getStatusCode() . '. Error: Unable to parse API response body as JSON.';
            }

            return 'API response HTTP status code: ' . $response->getStatusCode() . '. API Response body: ' . $response->getBody()->getContents();
        }

        if ($exception instanceof \JsonException) {
            return 'Error: Unable to parse API response body as JSON.';
        }

        return 'Unexpected error: ' . $exception->getMessage();
    }

    protected function handleRequestException(RequestException $exception): void
    {
        if (401 === $exception->getResponse()->getStatusCode()) {
            throw new MissingOrMalformedApiKey($this->formatError($exception), previous: $exception);
        }

        if (403 === $exception->getResponse()->getStatusCode()) {
            throw new InvalidApiAccessLevel($this->formatError($exception), previous: $exception);
        }

        if (404 === $exception->getResponse()->getStatusCode()) {
            throw new ConsignmentNotFound($this->formatError($exception), previous: $exception);
        }

        throw new ServerError($this->formatError($exception), previous: $exception);
    }
}
