<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exam;
use App\Entity\Category;

class ExamsTest extends ApiTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private static $entityManager;

    use RefreshDatabaseTrait;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    ##############################################################################
    # GET ITEM
    ##############################################################################

    /**
     * @group exams
     */
    public function testLoggedUserCanGetRestrictedExam()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        static::createClient()->request('GET', '/api/exams/2', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    /**
     * @group exams
     */
    public function testNonLoggedUserCanGetOpenExam()
    {
        static::createClient()->request('GET', '/api/exams/1');

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    /**
     * @group exams
     */
    public function testNonLoggedUserCantGetRestrictedExam()
    {
        static::createClient()->request('GET', '/api/exams/2');

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @group exams
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

        static::createClient()->request('GET', '/api/exams/2', ['auth_bearer' => $json['token']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceItemJsonSchema(Exam::class);
    }

    ##############################################################################
    # GET COLLECTION
    ##############################################################################

    /**
     * @group exams
     */
    public function testLoggedUserCanGetRestrictedExams()
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'user@gmail.com',
                'password' => 'Password1',
            ],
        ]);
        $json = $response->toArray();

        $result = static::createClient()->request('GET', '/api/exams?page=1', ['auth_bearer' => $json['token']]);
        $resultArray = $result->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(2, $resultArray['hydra:member']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Exam::class);
    }

    /**
     * @group exams
     */
    public function testNonLoggedUserCantGetRestrictedExams()
    {
        $result = static::createClient()->request('GET', '/api/exams?page=1');
        $resultArray = $result->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(1, $resultArray['hydra:member']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Exam::class);
    }

    /**
     * @group exams
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

        $result = static::createClient()->request('GET', '/api/exams?page=1', ['auth_bearer' => $json['token']]);
        $resultArray = $result->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(2, $resultArray['hydra:member']);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Exam::class);
    }

    ##############################################################################
    # POST
    ##############################################################################

    /**
     * @group exams
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
     * @group exams
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

    /**
     * @group exams
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

        static::createClient()->request('POST', '/api/exams', [
            'auth_bearer' => $json['token'],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'title' => '',
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

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    ##############################################################################
    # PATCH
    ##############################################################################

    /**
     * @group exams
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
     * @group exams
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

    // /**
    //  * @group exams
    //  */
    // public function testAddCategory()
    // {
    //     $response = static::createClient()->request('POST', '/authentication_token', [
    //         'headers' => ['Content-Type' => 'application/json'],
    //         'json' => [
    //             'email' => 'admin@gmail.com',
    //             'password' => 'Password1',
    //         ],
    //     ]);
    //     $json = $response->toArray();

    //     //$exam = static::createClient()->request('GET', '/api/exams/1', ['auth_bearer' => $json['token']]);
    //     //die(print_r($exam->getContent()));
    //     $category = new Category();
    //     $category->setLabel('Test label');

    //     $exam = new Exam();
    //     $exam->setTitle('Example');
    //     $exam->addCategory($category);

    //     self::$entityManager->persist($exam);
    //     self::$entityManager->flush();

    //     $this->assertResponseStatusCodeSame(200);
    //     $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    //     $this->assertMatchesResourceItemJsonSchema(Exam::class);
    // }

    // Test remove category

    ##############################################################################
    # DELETE
    ##############################################################################

    /**
     * @group exams
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
     * @group exams
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