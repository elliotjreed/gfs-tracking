<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking;

use ElliotJReed\GFS\Tracking\Entity\DeliveryAddress;
use ElliotJReed\GFS\Tracking\Entity\Geo;
use ElliotJReed\GFS\Tracking\Entity\TrackingEvent;
use ElliotJReed\GFS\Tracking\Exception\ServerError;
use ElliotJReed\GFS\Tracking\Exception\UnexpectedResponse;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class Consignment extends Tracking
{
    /**
     * @throws \DateMalformedStringException
     * @throws Exception\ConsignmentNotFound
     * @throws Exception\InvalidApiAccessLevel
     * @throws Exception\MissingOrMalformedApiKey
     * @throws Exception\ServerError
     * @throws UnexpectedResponse
     */
    public function getConsignment(string $carrier, string $consNo): Entity\Consignment
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

        $deliveryAddress = (new DeliveryAddress())
            ->setContact($consignmentData['deliveryAddress']['contact'])
            ->setCompany($consignmentData['deliveryAddress']['company'])
            ->setStreet($consignmentData['deliveryAddress']['street'])
            ->setCity($consignmentData['deliveryAddress']['city'])
            ->setCounty($consignmentData['deliveryAddress']['county'])
            ->setCountryCode($consignmentData['deliveryAddress']['countryCode'])
            ->setPostcode($consignmentData['deliveryAddress']['postcode']);

        if (isset($consignmentData['deliveryAddress']['geo'])) {
            $deliveryAddress->setGeo((new Geo())
                ->setLongitude($consignmentData['deliveryAddress']['geo']['longitude'])
                ->setLatitude($consignmentData['deliveryAddress']['geo']['latitude']));
        }

        $consignment = (new Entity\Consignment())
            ->setCarrier($consignmentData['carrier'])
            ->setGfsId($consignmentData['gfsId'])
            ->setConsNo($consignmentData['consNo'])
            ->setService($consignmentData['service'])
            ->setDespatchDate(new \DateTimeImmutable($consignmentData['despatchDate']))
            ->setShipmentRef($consignmentData['shipmentRef'])
            ->setDeliveryAddress($deliveryAddress)
            ->setStatus((new TrackingEvent())
                ->setText($consignmentData['status']['text'])
                ->setDateTime(new \DateTimeImmutable($consignmentData['status']['dateTime'])));

        if (isset($consignmentData['pod'])) {
            $consignment->setPod($this->requestProofOfDelivery($consignmentData['pod']['href']));
        }

        $consignment->setParcels($this->requestTrackingEvents($consignmentData['parcels']));

        return $consignment;
    }
}
