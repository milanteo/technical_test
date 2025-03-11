<?php

namespace App\Security;

enum JwtType: string {
    case ACCESS  = 'access';
    case REFRESH = 'refresh';
}