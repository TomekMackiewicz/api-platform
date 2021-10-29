<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class UsersTest extends ApiTestCase
{
    use RefreshDatabaseTrait; //ReloadDatabaseTrait RefreshDatabaseTrait;

    public static function setUpBeforeClass(): void
    {
        //self::$purgeWithTruncate = true;

        //$kernel = self::bootKernel();
        //$entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        // $conn = $entityManager->getConnection();
        // $sql = 'ALTER SEQUENCE user_id_seq RESTART WITH 1';
        // $stmt = $conn->prepare($sql);
        // $stmt->execute();

        // Purge all the fixtures data when the tests are finished
        //$purger = new ORMPurger($entityManager);
        // Purger mode 2 truncates, resetting autoincrements
        //$purger->setPurgeMode(2);
        //$purger->purge();
    }

    ##############################################################################
    # LOGIN
    ##############################################################################

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

    ##############################################################################
    # REGISTER
    ##############################################################################

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
                'username' => 'Test2'
            ]            
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

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
                'username' => 'Test2'
            ]            
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

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
                'username' => 'Test2'
            ]            
        ]); 
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }   

    ##############################################################################
    # DELETE
    ##############################################################################

    public function testNotLoggedUserCantDeleteUsers()
    {

    }

    public function testLoggedUserCantDeleteUsers()
    {
        
    }

    public function testAdminCanDeleteUsers()
    {
        
    } 

// testChangePassword
}