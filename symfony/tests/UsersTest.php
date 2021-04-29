<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use App\Entity\User;

class UsersTest extends ApiTestCase
{
    use RecreateDatabaseTrait; //ReloadDatabaseTrait RefreshDatabaseTrait;

    public static function setUpBeforeClass(): void
    {
        self::$purgeWithTruncate = true;
    }
    
    public function testLoginWithValidCredentials(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ]
        ]);

        $this->assertStringContainsString('token', $response->getContent());
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentials(): void
    {
        static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'fakeuser@gmail.com',
                'password' => 'Fakepass1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterWithValidEmailAndPassword()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user2@gmail.com',
                'password' => 'Password1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testRegisterWithInvalidEmail()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'invalidemail.com',
                'password' => 'Password1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'email: The email \'"invalidemail.com"\' is not a valid email.']);
    }

    public function testRegisterWithInvalidPassword()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user2@gmail.com',
                'password' => 'password1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'password: Password is required to be minimum 6 chars in length and to include at least one letter and one number.']);
    }

    public function testNotLoggedUserCantGetUsers()
    {
        static::createClient()->request('GET', '/api/users');
        
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    public function testLoggedUserCantGetUsers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();
        
        static::createClient()->request('GET', '/api/users?page=1', ['auth_bearer' => $json['token']]);       
        
        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    public function testNonLoggedUserCantGetUser()
    {
        static::createClient()->request('GET', '/api/users/1'); 
       
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    public function testLoggedUserCantGetUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();
        
        static::createClient()->request('GET', '/api/users/1', ['auth_bearer' => $json['token']]); 

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }    
    
//    testNotLoggedUserCantPatchUsers
//    testLoggedUserCantPatchUsers

//    testNotLoggedUserCantDeleteUsers
//    testLoggedUserCantDeleteUsers

    public function testAdminCanGetUsers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();
       
        static::createClient()->request('GET', '/api/users?page=1', ['auth_bearer' => $json['token']]); 
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testAdminCanGetUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();
        
        static::createClient()->request('GET', '/api/users/1', ['auth_bearer' => $json['token']]);       
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

//    testAdminCanPatchUsers
//    testAdminCanDeleteUsers
    
// testChangePassword
}