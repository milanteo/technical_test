<?php

namespace App\QueryDto;

use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class GetOrdersDto {

    public string $search = '';

    #[Choice(choices: [ 'date' ])]
    public string $orderBy = 'date';

    #[Choice(choices: [ 'asc', 'desc' ])]
    public string $orderDir = 'desc';

    #[GreaterThanOrEqual(1)]
    public int $page = 1;

    #[LessThanOrEqual(50)]
    public int $pageSize = 25;

}
