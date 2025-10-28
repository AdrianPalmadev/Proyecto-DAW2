<?php

namespace App\Controller;

use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[Route('/nurse', name: 'app_nurse')]
final class NurseController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/NurseController.php',
        ]);
    }

    #[Route('/login', name: 'app_nurse_login', methods: ['POST'])]
    public function login(Request $request, NurseRepository $repo): JsonResponse
    {
        $data = $request->toArray();

        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($usuario) || empty($password)) {
            return $this->json(['message' => 'usuario y password obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $nurse = $repo->login($usuario, $password);

        if (!$nurse) {
            return $this->json(['message' => 'No encontrado o credenciales incorrectas'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $nurse->getId(),
            'usuario' => $nurse->getUser(),
            'nombre' => $nurse->getName(),
        ]);
    }


    #[Route('/index', name: 'app_nurse_getall', methods: ['GET'])]
    public function getAll(NurseRepository $repo)
    {
        $nurses = $repo->getAll(); // o findAll() si usas Doctrine directamente

        $data = [];

        foreach ($nurses as $nurse) {
            $data[] = [
                'id' => $nurse->getId(),
                'usuario' => $nurse->getUser(),
                'nombre' => $nurse->getName(),
                'email' => $nurse->getEmail(),
                'trabajando' => $nurse->isWorking(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/name/{usuario}', name: 'app_nurse_findbyname', methods: ['GET'])]
    public function findByUser(string $usuario, NurseRepository $repo): JsonResponse
    {
        $nurse = $repo->findByUser($usuario);

        if (!$nurse) {
            return $this->json(['message' => 'No encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $nurse->getId(),
            'usuario' => $nurse->getUser(),
            'nombre' => $nurse->getName(),
        ]);
    }
}
