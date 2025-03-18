<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationDto {

    #[NotBlank()]
    #[Email()]
    public string $email;

    #[NotBlank()]
    public string $password;

}
