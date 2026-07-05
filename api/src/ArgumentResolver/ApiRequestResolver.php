<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\DTO\ApiRequestInterface;
use App\Exception\ApiValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if (!$type || !is_subclass_of($type, ApiRequestInterface::class)) {
            return [];
        }

        $data = [];
        if (in_array($request->getContentTypeFormat(), ['json', 'jsonld'], true)) {
            $data = json_decode($request->getContent(), true) ?? [];
        } else {
            $data = $request->request->all();
        }

        $dto = $this->serializer->denormalize($data, $type);

        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            throw new ApiValidationException($violations);
        }

        return [$dto];
    }
}
