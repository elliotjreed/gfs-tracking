<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class Location
{
    private string $locationText;
    private Geo $geo;

    public function getLocationText(): string
    {
        return $this->locationText;
    }

    public function setLocationText(string $locationText): self
    {
        $this->locationText = $locationText;

        return $this;
    }

    public function getGeo(): Geo
    {
        return $this->geo;
    }

    public function setGeo(Geo $geo): self
    {
        $this->geo = $geo;

        return $this;
    }
}
