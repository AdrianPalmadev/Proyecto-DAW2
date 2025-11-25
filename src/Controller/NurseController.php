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

    #[Route('/name/{name}', name: 'app_nurse_findbyuser', methods: ['GET'])]
    public function findByUser(string $name, NurseRepository $repo): JsonResponse
    {
        $nurse = $repo->findByUser($name);

        if (!$nurse) {
            return $this->json(['message' => 'No encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $nurse->getId(),
            'usuario' => $nurse->getUser(),
            'nombre' => $nurse->getName(),
        ]);
    }

    #[Route('/register', name: 'app_nurse_register', methods: ['POST'])]
    public function register(Request $request, NurseRepository $repo): JsonResponse
    {
        $data = $request->toArray();

        $name = $data['name'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        $working = $data['working'] ?? false;

        if (!$name || !$usuario || !$password || !$email) {
            return $this->json(['message' => 'Faltan campos obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        if ($repo->findByUser($usuario)) {
            return $this->json(['message' => 'El usuario ya existe'], Response::HTTP_BAD_REQUEST);
        }

        $nurse = new \App\Entity\Nurse();
        $nurse->setName($name);
        $nurse->setUser($usuario);
        $nurse->setPassword($password);
        $nurse->setEmail($email);
        $nurse->setWorking($working);

        $repo->create($nurse);

        return $this->json([
            'message' => 'Nurse creada correctamente',
            "name:" => $nurse->getName()
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/delete', name: 'app_nurse_delete', methods: ['DELETE'])]
    public function delete(Request $request, NurseRepository $repo): JsonResponse
    {

        $data = $request->toArray();

        $user = $data['usuario'] ?? null;

        $nurse = $repo->findByUser($user);

        if (!$nurse) {
            return $this->json(['message' => 'Nurse con el user ' . $user . ' no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $repo->delete($nurse);

        return $this->json(['message' => 'Se ha eliminado correctamente a ' . $nurse->getName()], Response::HTTP_OK);
    }

    #[Route(path: '/edit/{user}', name: 'app_nurse_edit', methods: ['PUT'])]
    public function edit(Request $request, string $user, NurseRepository $repo)
    {
        $nurse = $repo->findByUser($user);

        if (!$nurse) {
            return $this->json(['message' => 'Nurse con el user ' . $user . ' no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->toArray();

        $name = $data['name'] ?? $nurse->getName();
        $usuario = $data['usuario'] ?? $nurse->getUser();
        $password = $data['password'] ?? $nurse->getPassword();
        $email = $data['email'] ?? $nurse->getEmail();
        $working = $data['working'] ?? $nurse->isWorking();
        $nurse->setName($name);
        $nurse->setUser($usuario);
        $nurse->setPassword($password);
        $nurse->setEmail($email);
        $nurse->setWorking($working);

        $repo->edit($nurse);

        return $this->json(['message' => 'Nurse actualizado correctamente']);
    }
}
