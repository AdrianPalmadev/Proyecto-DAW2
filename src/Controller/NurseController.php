<?php

namespace App\Controller;

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
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $usuario = $data['usuario'] ?? null;
        $password = $data['password'] ?? null;

        $json = file_get_contents($this->getParameter('kernel.project_dir') . '/public/nurses.json');

        $data = json_decode($json, true); // <- ojo, el true hace que sea array

        $encuentro = null;
        foreach ($data as $nurse) {
            if ($nurse['usuario'] === $usuario && $nurse['password'] === $password) {
                $encuentro = $nurse;
                break;
            }
        }

        if ($encuentro == null) {
            return $this->json($encuentro, status: Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($encuentro, status: Response::HTTP_OK);
    }

    #[Route('/index', name: 'app_nurse_getall', methods: ['GET'])]
    public function getAll()
    {
        $json = file_get_contents($this->getParameter('kernel.project_dir') . '/public/nurses.json');

        $data = json_decode($json);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/name/{usuario}', name: 'app_nurse_findbyname', methods: ['GET'])]
    public function findByName($usuario)
    {

        $path = $this->getParameter('kernel.project_dir') . '/public/nurses.json';

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        $encuentro = null;
        foreach ($data as $nurse) {
            if ($nurse['usuario'] === $usuario) {
                $encuentro = $nurse;
                break;
            }
        }

        if ($encuentro == null) {
            return $this->json($encuentro, status: Response::HTTP_NOT_FOUND);
        }

        return $this->json($encuentro, status: Response::HTTP_OK);
    }
}
