<?php

namespace App\Controller;

use App\Service\CacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaginationService;

#[Route('/job')]
class JobController extends AbstractController
{
    public function __construct(
        private CacheService $cacheService,
        private PaginationService $paginationService
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

        try {
            $totalCount = $this->cacheService->getTotalCount();
        } catch (\RuntimeException $e) {
            return new Response(
                'Erreur de connexion à ElasticSearch: ' . $e->getMessage(),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $pagination = $this->paginationService->paginate($totalCount, $page, 10);

        if (!$pagination['isValid']) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        try {
            $jobs = $this->cacheService->getJobsFromBordeaux($page, $pagination['perPage']);
        } catch (\RuntimeException $e) {
            return new Response(
                'Erreur de connexion à ElasticSearch: ' . $e->getMessage(),
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        return $this->render('job/index.html.twig', [
            'jobs'        => $jobs,
            'pagination' => $pagination
        ]);
    }
}