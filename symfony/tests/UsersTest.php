<?php

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
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
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
        $this->assertJsonContains(['hydra:description' => 'email: The email \'"invalidemail.com"\' is not a valid email.']);
    }

    /**
     * @group user
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
        $this->assertJsonContains(['hydra:description' => 'password: Password is required to be minimum 6 chars in length and to include at least one letter and one number.']);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group user
     */
    public function testNotLoggedUserCantGetUsers()
    {
        static::createClient()->request('GET', '/api/users');
        
        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group user
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
     * @group user
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
     * @group user
     */
    public function testNonLoggedUserCantGetUser()
    {
        static::createClient()->request('GET', '/api/users/1'); 

        $this->assertResponseStatusCodeSame(401);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
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
     * @group user
     */
    public function testNotLoggedUserCantDeleteUser()
    {
        static::createClient()->request('DELETE', '/api/users/1'); 
        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @group user
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
     * @group user
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

}