<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\SharesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shares")
 */
class ShareController
{
    private SharesService $sharesService;

    public function __construct(SharesService $sharesService)
    {
        $this->sharesService = $sharesService;
    }

    /**
     * @Route("/{name}", methods={"GET"})
     */
    public function get(string $name): Response
    {
        $details = $this->sharesService->getDetails($name);

        return new JsonResponse([
            $details,
        ]);
    }

    /**
     * @Route("/{name}/purchase", methods={"POST"})
     */
    public function purchase(Request $request, string $name): Response
    {
        $user = $request->headers->get('X-User');
        $payload = json_decode($request->getContent(), true);
        $quantity = $payload['quantity'] ?? 0;

        if (!$user) {
            throw new BadRequestHttpException('Invalid user');
        }

        if (!is_numeric($quantity) || $quantity < 1) {
            throw new BadRequestHttpException('Quantity of shares needs to be positive');
        }

        $this->sharesService->purchase($user, $name, $quantity);

        return new JsonResponse([], Response::HTTP_CREATED);
    }
}