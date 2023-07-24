<?php

declare(strict_types=1);

namespace App\Account\Domain\Model;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

final class UserId
{
    public function __construct(private readonly UuidV7 $uuid)
    {
    }

    public static function generate(): self
    {
        return new self(Uuid::v7());
    }

    public static function fromString(string $value): self
    {
        return new self(UuidV7::fromString($value));
    }

    public function toString(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
