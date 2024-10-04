<?php

declare(strict_types=1);

namespace ElliotJReed\GFS\Tracking\Entity;

class File
{
    private ?string $mimeType = null;
    private ?string $stream = null;

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getStream(): ?string
    {
        return $this->stream;
    }

    public function setStream(?string $stream): self
    {
        $this->stream = $stream;

        return $this;
    }
}
