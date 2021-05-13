<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use App\Entity\User;
use App\Repository\UserRepository;

final class UserCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $cache;
    private $repository;

    public function __construct(AdapterInterface $cache, UserRepository $repository)
    {
        $this->cache = $cache;
        $this->repository = $repository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $cacheKey = 'users';
        $users = $this->cache->getItem($cacheKey);
        if (!$users->isHit()) {
            $users->set($this->repository->findAll());
            $users->expiresAfter(new DateInterval('PT1H'));
            $this->cache->save($users);
        }

        return $users->get('users');
    }
}