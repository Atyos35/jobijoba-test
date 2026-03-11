<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService
{
    private const CACHE_TTL = 300;

    public function __construct(
        private ElasticSearchService $elasticSearchService,
        private CacheInterface $cache
    ) {}

    public function getJobsFromBordeaux(int $page = 1, int $perPage = 10): array
    {
        $cacheKey = "jobs_bordeaux_p{$page}_n{$perPage}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page, $perPage) {
            $item->expiresAfter(self::CACHE_TTL);
            return $this->elasticSearchService->getJobsFromBordeaux($page, $perPage);
        });
    }

    public function getTotalCount(): int
    {
        return $this->cache->get('jobs_bordeaux_count', function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);
            return $this->elasticSearchService->getTotalCount();
        });
    }
}