<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class UsersTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    ##############################################################################
    # LOGIN
    ##############################################################################

    /**
     * @group users
     */
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

    /**
     * @group users
     */
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

    ##############################################################################
    # REGISTER
    ##############################################################################

    /**
     * @group users
     */
    public function testRegisterWithValidEmailAndPassword()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user2@gmail.com',
                'password' => 'Password1',
                'username' => 'Test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testRegisterWithInvalidEmail()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'invalidemail.com',
                'password' => 'Password1',
                'username' => 'Test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'email: validation.email']);
    }

    /**
     * @group users
     */
    public function testRegisterWithInvalidPassword()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user2@gmail.com',
                'password' => 'password1',
                'username' => 'Test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'password: validation.invalid_password']);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group users
     */
    public function testNotLoggedUserCantGetUsers()
    {
        static::createClient()->request('GET', '/api/users');

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group users
     */
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

    /**
     * @group users
     */
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

    ##############################################################################
    # GET ITEM
    ##############################################################################

    /**
     * @group users
     */
    public function testNonLoggedUserCantGetUser()
    {
        static::createClient()->request('GET', '/api/users/1');

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group users
     */
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

    /**
     * @group users
     */
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

    ##############################################################################
    # PATCH
    ##############################################################################

    /**
     * @group users
     */
    public function testNotLoggedUserCantPatchOtherUser()
    {
        static::createClient()->request('PATCH', '/api/users/2', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'Test2'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group users
     */
    public function testLoggedUserCantPatchOtherUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/users/1', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'username' => 'User2'
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group users
     */
    public function testLoggedUserCanPatchHimself()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/users/2', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'username' => 'User2'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    /**
     * @group users
     */
    public function testAdminCanPatchOtherUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/users/2', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'username' => 'User2'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }   

    ##############################################################################
    # DELETE
    ##############################################################################

    /**
     * @group users
     */
    public function testNotLoggedUserCantDeleteUser()
    {
        static::createClient()->request('DELETE', '/api/users/1'); 
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @group users
     */
    public function testLoggedUserCantDeleteUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/users/1', [
            'auth_bearer' => $json['token']
        ]);
        
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @group users
     */
    public function testAdminCanDeleteUser()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/users/2', [
            'auth_bearer' => $json['token']
        ]);
        
        $this->assertResponseStatusCodeSame(204);
    }

    ##############################################################################
    # VALIDATION
    ##############################################################################

    /**
     * @group users
     */
    public function testBlankUsername()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'ross@gmail.com',
                'password' => 'Password1',
                'username' => ''
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'username: validation.not_blank']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testUniqueUsername()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'monika@gmail.com',
                'password' => 'Password1',
                'username' => 'Admin'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'username: validation.unique']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


    /**
     * @group users
     */
    public function testBlankEmail()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => '',
                'password' => 'Password1',
                'username' => 'Rachel'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'email: validation.not_blank']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testUniqueEmail()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
                'username' => 'Chandler'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'email: validation.unique']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testRolesInvalidChoice()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'chandler@gmail.com',
                'password' => 'Password1',
                'username' => 'Chandler',
                'roles' => ['ROLE_CHANDLER']
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'roles: validation.invalid_choice']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testBlankPassword()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'chandler@gmail.com',
                'password' => '',
                'username' => 'Chandler'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'password: validation.not_blank']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testPasswordRegexLength()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'joey@gmail.com',
                'password' => 'Pass1',
                'username' => 'Joey'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'password: validation.invalid_password']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testPasswordRegexOneUppercase()
    {
        static::createClient()->request('POST', '/api/users', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'joey@gmail.com',
                'password' => 'nouppercase1',
                'username' => 'Joey'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'password: validation.invalid_password']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group users
     */
    public function testPasswordRegexOneNumber()
    {        static::createClient()->request('POST', '/api/users', [
        'headers' => ['Content-Type' => 'application/json'],
        'json' => [
            'email' => 'joey@gmail.com',
            'password' => 'Nonumber',
            'username' => 'Joey'
        ]
    ]);

    $this->assertResponseStatusCodeSame(422);
    $this->assertJsonContains(['hydra:description' => 'password: validation.invalid_password']);
    $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}