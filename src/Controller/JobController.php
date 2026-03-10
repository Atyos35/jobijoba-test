<?php

namespace App\Controller;

use App\Service\ElasticSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job')]
class JobController extends AbstractController
{
    public function __construct(
        private ElasticSearchService $elasticSearchService
    ) {}

    #[Route('', name: 'app_job')]
    public function redirectToDefault(): Response
    {
        return $this->redirectToRoute('app_bordeaux_job');
    }

    #[Route('/bordeaux', name: 'app_bordeaux_job')]
    #[Route('/bordeaux/page/{page}', name: 'app_bordeaux_job_page')]
    public function bordeauxJobs(int $page = 1): Response
    {
        $perPage = 10;

        try {
            $totalCount = $this->elasticSearchService->getTotalCount();
            $totalPages = max(1, (int) ceil($totalCount / $perPage));
        } catch (\RuntimeException $e) {
            return new Response(
                'Erreur de connexion à ElasticSearch: ' . $e->getMessage(),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        if ($page < 1 || $page > $totalPages) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        try {
            $jobs = $this->elasticSearchService->getJobsFromBordeaux($page, $perPage);
        } catch (\RuntimeException $e) {
            return new Response(
                'Erreur de connexion à ElasticSearch: ' . $e->getMessage(),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        return $this->render('job/index.html.twig', [
            'jobs'        => $jobs,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalCount'  => $totalCount,
        ]);
    }
}