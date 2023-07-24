<?php

declare(strict_types=1);

namespace App\Account\Domain\Model;

final class EmailAddress
{
    public function __construct(private readonly string $value)
    {
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
