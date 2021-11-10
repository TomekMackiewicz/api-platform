<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Exam;
use App\Repository\ExamRepository;

final class ExamCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $cache;
    private $repository;
    private $security;

    public function __construct(AdapterInterface $cache, ExamRepository $repository, Security $security)
    {
        $this->cache = $cache;
        $this->repository = $repository;
        $this->security = $security;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Exam::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $currentUser = $this->security->getUser();

        return (null !== $currentUser) ? $this->repository->findAll() : $this->repository->findBy(['restrictSubmissions' => false]);
    }
}