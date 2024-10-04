<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class ProofOfDelivery
{
    private ?string $signee;
    private ?\DateTimeImmutable $deliveryDateTime;
    private ?int $itemCount;
    private ?string $driverComment;
    private Location $location;
    /** @var \ElliotJReed\GFS\Tracking\Entity\File[] */
    private array $images = [];

    public function getSignee(): ?string
    {
        return $this->signee;
    }

    public function setSignee(?string $signee): self
    {
        $this->signee = $signee;

        return $this;
    }

    public function getDeliveryDateTime(): ?\DateTimeImmutable
    {
        return $this->deliveryDateTime;
    }

    public function setDeliveryDateTime(?\DateTimeImmutable $deliveryDateTime): self
    {
        $this->deliveryDateTime = $deliveryDateTime;

        return $this;
    }

    public function getItemCount(): ?int
    {
        return $this->itemCount;
    }

    public function setItemCount(?int $itemCount): self
    {
        $this->itemCount = $itemCount;

        return $this;
    }

    public function getDriverComment(): ?string
    {
        return $this->driverComment;
    }

    public function setDriverComment(?string $driverComment): self
    {
        $this->driverComment = $driverComment;

        return $this;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}
