<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\NotNull;

class PatchOrderDto {

    #[NotNull()]
    #[NotEqualTo('')]
    public string $name;

    public string $description;

}
