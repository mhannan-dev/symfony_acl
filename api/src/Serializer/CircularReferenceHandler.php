<?php

declare(strict_types=1);

namespace App\Serializer;

final class CircularReferenceHandler
{
    public function __invoke(object $object): mixed
    {
        return method_exists($object, 'getId') ? $object->getId() : (string) $object;
    }
}
