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
            return $this->json(['message' => 'username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $nurse = $repo->login($usuario, $password);

        if (!$nurse) {
            return $this->json(['message' => 'Not found or invalid credentials'], Response::HTTP_NOT_FOUND);
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
        $nurses = $repo->getAll();

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

    #[Route('/name/{name}', name: 'app_nurse_findByName', methods: ['GET'])]
    public function findByName(string $name, NurseRepository $repo): JsonResponse
    {
        $nurse = $repo->findByName($name);

        if (!$nurse) {
            return $this->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $nurse->getId(),
            'usuario' => $nurse->getUser(),
            'nombre' => $nurse->getName(),
        ]);
    }

    #[Route('/create', name: 'app_nurse_create', methods: ['POST'])]
    public function create(Request $request, NurseRepository $repo): JsonResponse
    {
        $data = $request->toArray();

        $name = $data['name'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        $working = $data['working'] ?? false;

        if (!$name || !$usuario || !$password || !$email) {
            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if ($repo->findByName($usuario)) {
            return $this->json(['message' => 'User already exists'], Response::HTTP_BAD_REQUEST);
        }

        $nurse = new \App\Entity\Nurse();
        $nurse->setName($name);
        $nurse->setUser($usuario);
        $nurse->setPassword($password);
        $nurse->setEmail($email);
        $nurse->setWorking($working);

        $repo->create($nurse);

        return $this->json([
            'message' => 'Nurse successfully created',
            "name:" => $nurse->getName()
        ], Response::HTTP_CREATED);
    }

    #[Route(path: '/{nurse}', name: 'app_nurse_remove', methods: ['DELETE'])]
    public function remove(String $nurse, NurseRepository $repo): JsonResponse
    {

        $nurse = $repo->findByName($nurse);

        if (!$nurse) {
            return $this->json(['message' => 'Nurse with user ' . $nurse . ' not found'], Response::HTTP_NOT_FOUND);
        }

        $repo->delete($nurse);

        return $this->json(['message' => 'Successfully removed ' . $nurse->getName()], Response::HTTP_OK);
    }

    #[Route(path: '/edit/{id}', name: 'app_nurse_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id, NurseRepository $repo)
    {
        $nurse = $repo->findById($id);

        if (!$nurse) {
            return $this->json(['message' => 'Nurse with id ' . $id . ' not found'], Response::HTTP_NOT_FOUND);
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

        return $this->json(['message' => 'Nurse successfully updated']);
    }
}
