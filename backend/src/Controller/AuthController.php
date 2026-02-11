<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

/**
 * AuthController
 * Maneja la autenticación simulada
 */
#[Route('/api')]
class AuthController extends AbstractController
{
    private const VALID_ROLES = ['customer', 'admin'];

    /**
     * Endpoint de login simulado
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un token simple e información del cliente',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'customerId', type: 'string'),
                new OA\Property(property: 'role', type: 'string'),
                new OA\Property(property: 'token', type: 'string')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Error de validación')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'customerId', type: 'string'),
                new OA\Property(property: 'role', type: 'string', enum: ['customer', 'admin'])
            ]
        )
    )]
    #[OA\Tag(name: 'Autenticación')]
    public function login(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validar entrada
        $constraints = new Assert\Collection([
            'customerId' => [
                new Assert\NotBlank(message: 'El ID de cliente es obligatorio'),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9_-]+$/',
                    'message' => 'El ID de cliente debe ser alfanumérico'
                ])
            ],
            'role' => [
                new Assert\NotBlank(message: 'El rol es obligatorio'),
                new Assert\Choice([
                    'choices' => self::VALID_ROLES,
                    'message' => 'El rol debe ser "customer" o "admin"'
                ])
            ]
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            
            return $this->json([
                'error' => 'Error de validación',
                'details' => $errors
            ], 400);
        }

        // Generar token simple (para MVP, solo un JSON codificado en base64)
        $token = base64_encode(json_encode([
            'customerId' => $data['customerId'],
            'role' => $data['role'],
            'timestamp' => time()
        ]));

        return $this->json([
            'customerId' => $data['customerId'],
            'role' => $data['role'],
            'token' => $token
        ]);
    }
}
