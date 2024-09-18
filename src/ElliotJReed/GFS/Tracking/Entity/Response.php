<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

/**
 * Class FinderResponseItem
 *
 * Represents a single consignment returned by the Finder API.
 */
class Response
{
    private string $gfsId;
    private string $carrier;
    private string $consNo;
    private string $despatchDate;
    private Link $self;
    private Link $consignment;
    /** @var Link[] */
    private array $links;

    /**
     * FinderResponseItem constructor.
     *
     * @param string $gfsId
     * @param string $carrier
     * @param string $consNo
     * @param string $despatchDate
     * @param Link $self
     * @param Link $consignment
     * @param Link[] $links
     */
    public function __construct(
        string $gfsId,
        string $carrier,
        string $consNo,
        string $despatchDate,
        Link $self,
        Link $consignment,
        array $links
    ) {
        $this->gfsId = $gfsId;
        $this->carrier = $carrier;
        $this->consNo = $consNo;
        $this->despatchDate = $despatchDate;
        $this->self = $self;
        $this->consignment = $consignment;
        $this->links = $links;
    }

    /**
     * Create a FinderResponseItem from an associative array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['gfsId'],
            $data['carrier'],
            $data['consNo'],
            $data['despatchDate'],
            Link::fromArray($data['self']),
            Link::fromArray($data['consignment']),
            array_map([Link::class, 'fromArray'], $data['links'] ?? [])
        );
    }

    public function getGfsId(): string
    {
        return $this->gfsId;
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function getConsNo(): string
    {
        return $this->consNo;
    }

    public function getDespatchDate(): string
    {
        return $this->despatchDate;
    }

    public function getSelf(): Link
    {
        return $this->self;
    }

    public function getConsignment(): Link
    {
        return $this->consignment;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
