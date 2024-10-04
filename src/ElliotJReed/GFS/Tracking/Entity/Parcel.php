<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class Parcel
{
    /**
     * @var \ElliotJReed\GFS\Tracking\Entity\TrackingEvent[]
     */
    private array $trackingEvents;

    public function getTrackingEvents(): array
    {
        return $this->trackingEvents;
    }

    public function setTrackingEvents(array $trackingEvents): self
    {
        $this->trackingEvents = $trackingEvents;

        return $this;
    }
}
