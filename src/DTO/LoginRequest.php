<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest implements InertiaRequestInterface
{
    #[Assert\NotBlank(message: 'Email address is required.')]
    #[Assert\Email(message: 'Please provide a valid email address.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'Password is required.')]
    public string $password = '';
    
    public bool $remember = false;
}
