<?php

namespace App\QueryDto;

use Symfony\Component\Validator\Constraints\Choice;

class GetOrdersDto {

    public string $search = '';

    #[Choice(choices: [ 'date' ])]
    public string $orderBy = 'date';

    #[Choice(choices: [ 'asc', 'desc' ])]
    public string $orderDir = 'desc';

}
