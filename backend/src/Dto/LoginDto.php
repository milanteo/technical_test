<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class LoginDto {

    #[NotBlank()]
    public string $email;

    #[NotBlank()]
    public string $password;

}
