<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/login', [
            'json' => []
        ]);

        self::assertResponseIsSuccessful();
    }
}
