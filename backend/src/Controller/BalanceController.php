<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/balance")
 */
class BalanceController
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @Route("", name="balance_show", methods={"GET", "OPTIONS"})
     */
    public function getBalance(Request $request): Response
    {
        $user = $request->headers->get('X-User');

        return new JsonResponse([
            'balance' => $this->transactionService->getCurrentBalance($user),
        ]);
    }

    /**
     * @Route("/transaction", name="balance_transaction", methods={"POST", "OPTIONS"})
     */
    public function createTransaction(Request $request): Response
    {
        $user = $request->headers->get('X-User');
        $payload = json_decode($request->getContent(), true);
        $balance = $payload['balance'] ?? null;

        if (!is_numeric($balance)) {
            throw new BadRequestHttpException('Invalid balance value');
        }

        $this->transactionService->push($user, $balance);

        return new JsonResponse([
            'balance' => $this->transactionService->getCurrentBalance($user),
        ]);
    }
}
