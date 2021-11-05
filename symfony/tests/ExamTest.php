<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use App\Entity\Exam;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class ExamTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    ##############################################################################
    # GET ITEM
    ##############################################################################

    /**
     * @group exam
     */
    public function testUserCanGetExam()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/exams/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    /**
     * @group exam
     */
    public function testAdminCanGetExam()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/exams/1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group exam
     */
    public function testUserCanGetExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/exams?page=1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Exam::class);
    }

    /**
     * @group exam
     */
    public function testAdminCanGetExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/exams?page=1', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Exam::class);
    }

    ##############################################################################
    # POST
    ##############################################################################

    /**
     * @group exam
     */
    public function testUserCantPostExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/exams', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Friends quiz',
                'description' => 'The Ultimate "Friends" Trivia Quiz',
                'summary' => 'Are you a true fan?',
                'duration' => 3600,
                'nextSubmissionAfter' => 3600,
                'ttl' => 36000,
                'usePagination' => true,
                'questionsPerPage' => 5,
                'shuffleQuestions' => false,
                'immediateAnswers' => false,
                'restrictSubmissions' => false,
                'allowedSubmissions' => 2
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group exam
     */
    public function testAdminCanPostExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('POST', '/api/exams', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => 'Friends quiz',
                'description' => 'The Ultimate "Friends" Trivia Quiz',
                'summary' => 'Are you a true fan?',
                'duration' => 3600,
                'nextSubmissionAfter' => 3600,
                'ttl' => 36000,
                'usePagination' => true,
                'questionsPerPage' => 5,
                'shuffleQuestions' => false,
                'immediateAnswers' => false,
                'restrictSubmissions' => false,
                'allowedSubmissions' => 2
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    ##############################################################################
    # PATCH
    ##############################################################################

    /**
     * @group exam
     */
    public function testUserCantPatchExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/exams/1', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => 'New Friends quiz',
                'description' => 'The Ultimate "Friends" Quiz',
                'summary' => 'Are you a true fan???',
                'duration' => 7200,
                'nextSubmissionAfter' => 7200,
                'ttl' => 72000,
                'usePagination' => false,
                'questionsPerPage' => 0,
                'shuffleQuestions' => true,
                'immediateAnswers' => true,
                'restrictSubmissions' => true,
                'allowedSubmissions' => 0
            ]
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
    }

    /**
     * @group exam
     */
    public function testAdminCanPatchExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('PATCH', '/api/exams/1', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'title' => 'New Friends quiz',
                'description' => 'The Ultimate "Friends" Quiz',
                'summary' => 'Are you a true fan???',
                'duration' => 7200,
                'nextSubmissionAfter' => 7200,
                'ttl' => 72000,
                'usePagination' => false,
                'questionsPerPage' => 0,
                'shuffleQuestions' => true,
                'immediateAnswers' => true,
                'restrictSubmissions' => true,
                'allowedSubmissions' => 0
            ]
        ]); 

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    ##############################################################################
    # DELETE
    ##############################################################################

    /**
     * @group exam
     */
    public function testUserCantDeleteExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/exams/1', [
            'auth_bearer' => $json['token']
        ]);
        
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * @group exam
     */
    public function testAdminCanDeleteExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('DELETE', '/api/exams/1', [
            'auth_bearer' => $json['token']
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}