<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidad Pago
 * Representa un pago por un pedido (simulado)
 */
#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public const METHOD_SIMULATED = 'simulated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Order::class, inversedBy: 'payment')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(
        choices: [self::STATUS_PENDING, self::STATUS_COMPLETED, self::STATUS_FAILED],
        message: 'Estado de pago inválido'
    )]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'string', length: 50)]
    private string $method = self::METHOD_SIMULATED;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive(message: 'El monto del pago debe ser mayor a 0')]
    private string $amount;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $processedAt = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        
        // Automatically set processedAt when payment is completed
        if ($status === self::STATUS_COMPLETED && $this->processedAt === null) {
            $this->processedAt = new \DateTime();
        }
        
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getProcessedAt(): ?\DateTimeInterface
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeInterface $processedAt): self
    {
        $this->processedAt = $processedAt;
        return $this;
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->processedAt = new \DateTime();
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->status = self::STATUS_FAILED;
        $this->processedAt = new \DateTime();
    }
}
