<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidad Producto
 * Representa un producto en el catálogo
 */
#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'El nombre del producto es obligatorio')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'El nombre del producto no puede tener más de {{ limit }} caracteres'
    )]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'El precio es obligatorio')]
    #[Assert\Positive(message: 'El precio debe ser mayor a 0')]
    private string $price;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'El stock es obligatorio')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'El stock no puede ser negativo'
    )]
    private int $stock;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'product')]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * Disminuir stock por una cantidad dada
     * @throws \Exception si el stock es insuficiente
     */
    public function decreaseStock(int $quantity): void
    {
        if ($this->stock < $quantity) {
            throw new \Exception(sprintf(
                'Stock insuficiente para el producto %s. Disponible: %d, Solicitado: %d',
                $this->name,
                $this->stock,
                $quantity
            ));
        }
        $this->stock -= $quantity;
    }

    /**
     * Verificar si el producto tiene stock suficiente
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }
}
