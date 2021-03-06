<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ShareRepository;
use App\Service\SharesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/shares")
 */
class ShareController
{
    private SharesService $sharesService;
    private ShareRepository $repository;

    public function __construct(SharesService $sharesService, ShareRepository $repository)
    {
        $this->sharesService = $sharesService;
        $this->repository = $repository;
    }

    /**
     * @Route("", name="share_list", methods={"GET", "OPTIONS"})
     */
    public function list(Request $request): Response
    {
        $user = $request->headers->get('X-User');

        $shares = $this->repository->listByUser($user);
        $response = [];

        foreach ($shares as $share) {
            $totalValue = null;
            try {
                $details = $this->sharesService->getDetails($share->getName());
                $totalValue = $details->getPrice() * $share->getQuantity();
            } catch (Throwable $exception) {}

            $response[] = [
                'id' => $share->getId(),
                'name' => $share->getName(),
                'quantity' => $share->getQuantity(),
                'value' => $totalValue,
                'last_update' => $share->getUpdatedAt()->format('c'),
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/{name}", name="share_detail", methods={"GET", "OPTIONS"})
     */
    public function get(string $name): Response
    {
        $details = $this->sharesService->getDetails($name);

        return new JsonResponse($details->jsonSerialize());
    }

    /**
     * @Route("/{name}/purchase", name="share_purchase", methods={"POST", "OPTIONS"})
     */
    public function purchase(Request $request, string $name): Response
    {
        $user = $request->headers->get('X-User');
        $payload = json_decode($request->getContent(), true);
        $quantity = $payload['quantity'] ?? 0;

        if (!is_numeric($quantity) || $quantity < 1) {
            throw new BadRequestHttpException('Quantity of shares to purchase needs to be positive');
        }

        $transaction = $this->sharesService->purchase($user, $name, $quantity);

        return new JsonResponse([
            'transaction_value' => abs($transaction->getBalance()),
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{name}/sell", name="share_sale", methods={"POST", "OPTIONS"})
     */
    public function sell(Request $request, string $name): Response
    {
        $user = $request->headers->get('X-User');
        $payload = json_decode($request->getContent(), true);
        $quantity = $payload['quantity'] ?? 0;

        if (!is_numeric($quantity) || $quantity < 1) {
            throw new BadRequestHttpException('Quantity of shares to sell needs to be positive');
        }

        $transaction = $this->sharesService->sell($user, $name, $quantity);

        return new JsonResponse([
            'transaction_value' => $transaction->getBalance(),
        ]);
    }
}
