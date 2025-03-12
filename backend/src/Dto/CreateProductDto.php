<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

class CreateProductDto {

    #[NotBlank()]
    public string $name;

    #[NotBlank()]
    public float $price;

}
