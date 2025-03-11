<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationDto {

    #[NotBlank()]
    public string $email;

    #[NotBlank()]
    public string $password;

}
