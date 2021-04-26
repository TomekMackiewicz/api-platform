<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UsersTest extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testLoginWithValidCredentials(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'user@gmail.com',
            'password' => 'Password1',
        ]]);

        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'fakeuser@gmail.com',
            'password' => 'Fakepass1',
        ]]);

        $this->assertResponseStatusCodeSame(401);
    }
}