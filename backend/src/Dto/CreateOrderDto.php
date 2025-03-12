<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use App\Dto\CreateProductDto;
use Symfony\Component\Validator\Constraints\Valid;

class CreateOrderDto {

    #[NotBlank()]
    public string $name;

    public string $description;

    /** @var CreateProductDto[] $products */
    #[Valid()]
    public array $products;

}
