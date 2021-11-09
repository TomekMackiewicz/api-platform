<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Answer;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AnswersTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    ##############################################################################
    # GET ITEM
    ##############################################################################

    /**
     * @group answers
     */
    public function testUserCantGetAnswer()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/answers/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group answers
     */
    public function testAdminCanGetAnswer()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/answers/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Answer::class);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group answers
     */
    public function testUserCantGetAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/answers?page=1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group answers
     */
    public function testAdminCanGetAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/answers?page=1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Answer::class);
    }

    ##############################################################################
    # POST
    ##############################################################################

    /**
     * @group answers
     */
    public function testUserCantPostAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/answers', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Ross',
                'isCorrect' => true,
                'message' => 'Test',
                'points' => 3,
                'question' => 1
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group answers
     */
    public function testAdminCanPostAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/answers', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Ross',
                'isCorrect' => true,
                'message' => 'Test',
                'points' => 3,
                'question' => '/api/questions/1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * @group answers
     */
    public function testEmptyTitle()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/answers', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => '',
                'isCorrect' => true,
                'message' => 'Test',
                'points' => 3,
                'question' => '/api/questions/1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    ##############################################################################
    # PATCH
    ##############################################################################

    /**
     * @group answers
     */
    public function testUserCantPatchAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/answers/1', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => 'Test title 2'
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group answers
     */
    public function testAdminCanPatchAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/answers/1', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => 'Test title 2'
            ]
        ]); 

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Answer::class);
    }

    ##############################################################################
    # DELETE
    ##############################################################################

    /**
     * @group answers
     */
    public function testUserCantDeleteAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/answers/1', [
            'auth_bearer' => $json['token']
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @group answers
     */
    public function testAdminCanDeleteAnswers()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/answers/1', [
            'auth_bearer' => $json['token']
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}