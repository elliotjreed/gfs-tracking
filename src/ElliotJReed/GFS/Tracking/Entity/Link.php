<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

/**
 * Class Link
 *
 * Represents a hypermedia link.
 */
class Link
{
    private string $rel;
    private string $href;

    /**
     * Link constructor.
     *
     * @param string $rel
     * @param string $href
     */
    public function __construct(string $rel, string $href)
    {
        $this->rel = $rel;
        $this->href = $href;
    }

    /**
     * Create a Link from an associative array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['rel'],
            $data['href']
        );
    }

    // Getters for each property

    public function getRel(): string
    {
        return $this->rel;
    }

    public function getHref(): string
    {
        return $this->href;
    }
}
