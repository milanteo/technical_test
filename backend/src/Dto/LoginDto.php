<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginDto {

    #[NotBlank()]
    #[Email()]
    public string $email;

    #[NotBlank()]
    public string $password;

}
