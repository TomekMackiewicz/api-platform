<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use App\Entity\Exam;

final class ExamCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $cache;
    private $repository;
    private $security;
    private $collectionExtensions;
    private $managerRegistry;

    public function __construct(AdapterInterface $cache, Security $security, iterable $collectionExtensions, ManagerRegistry $managerRegistry)
    {
        $this->cache = $cache;
        $this->security = $security;
        $this->collectionExtensions = $collectionExtensions;
        $this->managerRegistry = $managerRegistry;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Exam::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $repository = $manager->getRepository($resourceClass);
        $currentUser = $this->security->getUser();

        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }

        if (null !== $currentUser) {
            $queryBuilder = $repository->createQueryBuilder('e');
        } else {
            $queryBuilder = $repository->createQueryBuilder('e')->where('e.restrictSubmissions = :restrictSubmissions')->setParameter('restrictSubmissions', false);
        }

        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName, $context)) {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}