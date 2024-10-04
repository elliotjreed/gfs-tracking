<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class Consignment
{
    private string $gfsId;
    private string $carrier;
    private string $service;
    private string $consNo;
    private \DateTimeImmutable $despatchDate;
    private ?string $shipmentRef;
    private ?DeliveryAddress $deliveryAddress = null;
    /** @var \ElliotJReed\GFS\Tracking\Entity\Parcel[] */
    private array $parcels = [];
    private ?TrackingEvent $status = null;
    private ?ProofOfDelivery $pod = null;

    public function getGfsId(): string
    {
        return $this->gfsId;
    }

    public function setGfsId(string $gfsId): self
    {
        $this->gfsId = $gfsId;

        return $this;
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function setCarrier(string $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getConsNo(): string
    {
        return $this->consNo;
    }

    public function setConsNo(string $consNo): self
    {
        $this->consNo = $consNo;

        return $this;
    }

    public function getDespatchDate(): \DateTimeImmutable
    {
        return $this->despatchDate;
    }

    public function setDespatchDate(\DateTimeImmutable $despatchDate): self
    {
        $this->despatchDate = $despatchDate;

        return $this;
    }

    public function getShipmentRef(): ?string
    {
        return $this->shipmentRef;
    }

    public function setShipmentRef(?string $shipmentRef): self
    {
        $this->shipmentRef = $shipmentRef;

        return $this;
    }

    public function getDeliveryAddress(): ?DeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?DeliveryAddress $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getParcels(): array
    {
        return $this->parcels;
    }

    public function setParcels(array $parcels): self
    {
        $this->parcels = $parcels;

        return $this;
    }

    public function getStatus(): ?TrackingEvent
    {
        return $this->status;
    }

    public function setStatus(?TrackingEvent $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPod(): ?ProofOfDelivery
    {
        return $this->pod;
    }

    public function setPod(?ProofOfDelivery $pod): self
    {
        $this->pod = $pod;

        return $this;
    }
}
