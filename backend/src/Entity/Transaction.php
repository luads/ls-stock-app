<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\Table(name="user_transaction")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $user;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=5)
     */
    private float $balance;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Transaction
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): Transaction
    {
        $this->user = $user;
        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): Transaction
    {
        $this->balance = $balance;
        return $this;
    }
}
