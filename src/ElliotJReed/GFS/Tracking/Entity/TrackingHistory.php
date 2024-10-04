<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class TrackingHistory
{
    /** @var TrackingEvent[] */
    public $events = [];

    /** @var Link */
    public $self;

    /** @var Link */
    public $parcel;

    /** @var Link */
    public $consignment;

    /** @var Link[] */
    public $links = [];
}
