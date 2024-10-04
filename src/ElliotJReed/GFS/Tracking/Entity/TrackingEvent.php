<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class TrackingEvent
{
    private \DateTimeImmutable $dateTime;
    private string $text;
    private ?Location $location = null;

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeImmutable $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
