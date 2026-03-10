<?php

namespace App\Service;

use App\Entity\Job;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ElasticSearchService
{
    private Client $client;
    private string $indexName;

    public function __construct(
        string $elasticSearchUrl,
        string $elasticSearchIndex = 'job_fr'
    ) {
        $this->client = new Client([
            'base_uri' => $elasticSearchUrl,
            'timeout'  => 10.0,
        ]);
        $this->indexName = $elasticSearchIndex;
    }

    public function getJobsFromBordeaux(
        int $page = 1,
        int $perPage = 10
    ): array {
        $from = ($page - 1) * $perPage;

        $query = [
            'query' => [
                'bool' => [
                    'filter' => [
                        'term' => ['localities.city.keyword' => 'Bordeaux']
                    ]
                ]
            ],
            'from' => $from,
            'size' => $perPage,
            'sort' => ['date' => ['order' => 'desc']]
        ];

        try {
            $response = $this->client->request(
                'POST',
                '/' . $this->indexName . '/_search',
                ['json' => $query]
            );

            $data = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return $this->mapResults($data['hits']['hits']);
        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                'ElasticSearch error: ' . $e->getMessage()
            );
        }
    }

    public function getTotalCount(): int
    {
        try {
            $response = $this->client->request(
                'POST',
                '/' . $this->indexName . '/_count',
                [
                    'json' => [
                        'query' => [
                            'match' => ['city' => 'Bordeaux']
                        ]
                    ]
                ]
            );

            $data = json_decode(
                $response->getBody()->getContents(),
                true
            );

            return $data['count'] ?? 0;
        } catch (GuzzleException $e) {
            return 0;
        }
    }

    private function mapResults(array $hits): array
    {
        return array_map(function ($hit) {
            $source = $hit['_source'];

            return new Job(
                $source['title']                    ?? 'Sans titre',
                $source['description']              ?? 'Non spécifiéé',
                $source['localities'][0]['city']    ?? 'Non spécifié',
                $source['contract_type']            ?? 'Non spécifié',
                $source['company']                  ?? 'Non spécifiée'
            );
        }, $hits);
    }
}