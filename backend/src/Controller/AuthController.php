<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth', format: 'json')]
final class AuthController extends AbstractController {

    #[Route('/login', name: 'app_login')]
    public function appLogin(): JsonResponse {

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }
}
