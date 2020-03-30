<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return new JsonResponse([
            'description' => 'Stock Exchange API',
        ]);
    }
}
