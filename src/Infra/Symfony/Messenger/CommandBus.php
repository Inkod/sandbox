<?php

declare(strict_types=1);

namespace App\Infra\Symfony\Messenger;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus
{
    use HandleTrait {
        handle as private handleMessage;
    }

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function handle(object $message): mixed
    {
        try {
            return $this->handleMessage($message);
        } catch (HandlerFailedException $e) {
            // code below allows to throw the current exception raised by the handler
            $nested = $e->getNestedExceptions();

            if ([] === $nested) {
                throw new \LogicException('Bus must have one nested exception', 0, $e);
            }

            if (\count($nested) > 1) {
                throw new \LogicException('Bus cannot manage more than one nested exception from Symfony Messenger', 0, $e);
            }

            throw current($nested);
        }
    }
}
