<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Filesystem\Filesystem;

use App\Entity\MediaObject;

class MediaObjectTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public static function setUpBeforeClass(): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir('public/media/test');
    }

    public static function tearDownAfterClass(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove('public/media/test');
    }

    // ##############################################################################
    // # AUTH
    // ##############################################################################

    /**
     * @group media
     */
    public function testNonLoggedUserAccessToMediaObject()
    {
        static::createClient()->request('GET', '/api/media_objects?page=1');

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @group media
     */
    public function testLoggedUserAccessToMediaObject()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/media_objects/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    ##############################################################################
    # GET ITEM
    ##############################################################################

    /**
     * @group media
     */
    public function testGetMediaObject()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/media_objects/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(MediaObject::class);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group media
     */
    public function testGetMediaObjects()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        $result = static::createClient()->request('GET', '/api/media_objects?page=1', ['auth_bearer' => $json['token']]);
        $resultArray = $result->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $resultArray['hydra:member']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(MediaObject::class);
    }

    ##############################################################################
    # POST
    ##############################################################################

    /**
     * @group media
     */
    public function testPostMediaObject()
    {
        $filesystem = new Filesystem();
        $filesystem->copy('fixtures/files/original/friends.jpg', 'fixtures/files/friends.jpg');

        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        $file = new UploadedFile('fixtures/files/friends.jpg', 'friends.jpg');

        static::createClient()->request('POST', '/api/media_objects', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                ],
                'files' => [
                    'file' => $file
                ]
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(MediaObject::class);
        $this->assertJsonContains([
            'id' => 2
        ]);
    }

    /**
     * @group media
     */
    public function testPostMediaObjectWithInvalidMediaType()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        $file = new UploadedFile('fixtures/files/original/friends-format-test.txt', 'friends-format-test.txt');

        static::createClient()->request('POST', '/api/media_objects', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                ],
                'files' => [
                    'file' => $file
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'file: validation.mime_type']);
    }

    /**
     * @group media
     */
    public function testPostMediaObjectUploadLimit()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        $file = new UploadedFile('fixtures/files/original/friends-size-test.gif', 'friends-size-test.gif');

        static::createClient()->request('POST', '/api/media_objects', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                ],
                'files' => [
                    'file' => $file
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'file: validation.max_upload_size']);
    }

    /**
     * @group media
     */
    public function testAppendToQuestion()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/questions', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'label' => 'How many sisters does Joey have?',
                'description' => 'I said seven!',
                'type' => 'radio',
                'hint' => 'Or six?',
                'isRequired' => true,
                'shuffleAnswers' => false,
                'images' => ['/api/media_objects/1']
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'images' => [
                '/api/media_objects/1'
            ]
        ]);
    }

    /**
     * @group media
     */
    public function testAppendToAnswer()
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
                'question' => '/api/questions/1',
                'images' => ['/api/media_objects/1']
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'images' => [
                '/api/media_objects/1'
            ]
        ]);
    }

    ##############################################################################
    # DELETE
    ##############################################################################

    /**
     * @group media
     */
    public function testDeleteMediaObject()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1'
            ]
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/media_objects/1', [
            'auth_bearer' => $json['token']
        ]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(MediaObject::class)->findOneBy(['id' => 1])
        );
    }
}