<?php

declare(strict_types=1);

namespace App\Account\Infra\Doctrine\Dbal;

use App\Account\Domain\Model\EmailAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class EmailAddressType extends Type
{
    public function getName(): string
    {
        return 'email';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value instanceof EmailAddress || null === $value) {
            return $value;
        }

        if (!\is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', EmailAddress::class]);
        }

        return new EmailAddress($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof EmailAddress) {
            return $value->toString();
        }

        if (is_string($value)) {
            return $value;
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', EmailAddress::class]);
    }
}
