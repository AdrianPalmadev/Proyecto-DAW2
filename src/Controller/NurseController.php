<?php

namespace App\Controller;

use App\Entity\Nurse;
use App\Repository\NurseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[Route('/nurse')]
final class NurseController extends AbstractController
{
    #[Route('/login', methods: ['POST'])]
    public function login(Request $request, NurseRepository $repo): JsonResponse
    {
        $data = $request->toArray();

        $user = $data['user'] ?? null;
        $password = $data['password'] ?? null;

        if (!$user || !$password) {
            return $this->json(
                ['message' => 'user and password are required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $nurse = $repo->login($user, $password);

        if (!$nurse) {
            return $this->json(
                ['message' => 'Not found or invalid credentials'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            'id' => $nurse->getId(),
            'user' => $nurse->getUser(),
            'name' => $nurse->getName(),
            'email' => $nurse->getEmail(),
            'working' => $nurse->isWorking(),
            'imageUrl' => $nurse->getImageUrl(),
        ]);
    }

    #[Route('/index', methods: ['GET'])]
    public function getAll(NurseRepository $repo): JsonResponse
    {
        $nurses = $repo->getAll();
        $data = [];

        foreach ($nurses as $nurse) {
            $data[] = [
                'id' => $nurse->getId(),
                'user' => $nurse->getUser(),
                'name' => $nurse->getName(),
                'email' => $nurse->getEmail(),
                'working' => $nurse->isWorking(),
                'imageUrl' => $nurse->getImageUrl(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/name/{name}', methods: ['GET'])]
    public function findByName(string $name, NurseRepository $repo): JsonResponse
    {
        $nurses = $repo->findByName($name);

        if (empty($nurses)) {
            return $this->json(
                ['message' => 'No nurses found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = [];

        foreach ($nurses as $nurse) {
            $data[] = [
                'id' => $nurse->getId(),
                'user' => $nurse->getUser(),
                'name' => $nurse->getName(),
                'email' => $nurse->getEmail(),
                'working' => $nurse->isWorking(),
                'imageUrl' => $nurse->getImageUrl(),
            ];
        }

        return $this->json($data);
    }


    #[Route('/create', methods: ['POST'])]
    public function create(Request $request, NurseRepository $repo): JsonResponse
    {
        $data = $request->toArray();

        $name = $data['name'] ?? null;
        $user = $data['user'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        $working = (bool)($data['working'] ?? false);
        $imageUrl = $data['imageUrl'] ?? null;

        if (!$name || !$user || !$password || !$email) {
            return $this->json(
                ['message' => 'Missing required fields'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($repo->findByUser($user)) {
            return $this->json(
                ['message' => 'User already exists'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $nurse = new Nurse();
        $nurse->setName($name);
        $nurse->setUser($user);
        $nurse->setPassword($password);
        $nurse->setEmail($email);
        $nurse->setWorking($working);
        $nurse->setImageUrl($imageUrl);

        $repo->create($nurse);

        return $this->json([
            'id' => $nurse->getId(),
            'user' => $nurse->getUser(),
            'name' => $nurse->getName(),
            'email' => $nurse->getEmail(),
            'working' => $nurse->isWorking(),
            'imageUrl' => $nurse->getImageUrl(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function edit(Request $request, int $id, NurseRepository $repo): JsonResponse
    {
        $nurse = $repo->findById($id);

        if (!$nurse) {
            return $this->json(
                ['message' => 'Nurse not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $request->toArray();

        $nurse->setName($data['name'] ?? $nurse->getName());
        $nurse->setUser($data['user'] ?? $nurse->getUser());
        $nurse->setPassword($data['password'] ?? $nurse->getPassword());
        $nurse->setEmail($data['email'] ?? $nurse->getEmail());
        $nurse->setWorking((bool)($data['working'] ?? $nurse->isWorking()));
        $nurse->setImageUrl($data['imageUrl'] ?? $nurse->getImageUrl());

        $repo->edit($nurse);

        return $this->json([
            'message' => 'Nurse successfully updated',
            'imageUrl' => $nurse->getImageUrl(),
        ]);
    }
}
