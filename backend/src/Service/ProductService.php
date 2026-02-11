<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ProductService
 * Maneja la lógica de negocio para la gestión de productos
 */
class ProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Buscar todos los productos con paginación y filtrado
     *
     * @param array $filters Filtros a aplicar (búsqueda, etc.)
     * @param int $page Número de página actual
     * @param int $limit Ítems por página
     * @param string $sort Campo de ordenación y dirección (ej., 'name:asc')
     * @return array Resultados paginados con metadatos
     */
    public function findAll(array $filters = [], int $page = 1, int $limit = 10, string $sort = 'id:asc'): array
    {
        [$sortField, $sortDir] = $this->parseSortParameter($sort);
        
        $queryBuilder = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p');

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $queryBuilder->where('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        // Apply sorting
        $queryBuilder->orderBy('p.' . $sortField, $sortDir);

        // Apply pagination
        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = (int) ceil($totalItems / $limit);

        return [
            'data' => iterator_to_array($paginator),
            'meta' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Buscar producto por ID
     */
    public function findById(int $id): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->find($id);
    }

    /**
     * Crear un nuevo producto
     */
    public function create(array $data): Product
    {
        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description'] ?? null);
        $product->setPrice((string) $data['price']);
        $product->setStock((int) $data['stock']);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * Analizar el parámetro de ordenación en campo y dirección
     */
    private function parseSortParameter(string $sort): array
    {
        $parts = explode(':', $sort);
        $field = $parts[0] ?? 'id';
        $direction = strtoupper($parts[1] ?? 'ASC');

        // Validate field to prevent SQL injection
        $allowedFields = ['id', 'name', 'price', 'stock', 'createdAt'];
        if (!in_array($field, $allowedFields)) {
            $field = 'id';
        }

        // Validate direction
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $direction = 'ASC';
        }

        return [$field, $direction];
    }
}
