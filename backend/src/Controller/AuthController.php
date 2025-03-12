<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Dto\RegistrationDto;
use App\Repository\UserRepository;
use App\Security\JwtType;
use App\Security\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth', format: 'json')]
final class AuthController extends AbstractController {

    #[Route('/login', name: 'app_login', methods: [ Request::METHOD_POST ])]
    public function appLogin(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_UNAUTHORIZED)] 
        LoginDto $dto, 
        SecurityService $security,
        UserRepository $users,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {

        $user = $users->findOneBy([ 'email' => $dto->email ]);

        if(!$user || !$hasher->isPasswordValid($user, $dto->password)) {

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid credentials!');
        }

        $response = $this->json([
            "accessToken"  => $security->encodeJsonWebToken(JwtType::ACCESS,  $user)
        ]);

        $response->headers->setCookie(new Cookie(
            'refreshToken', 
            $security->encodeJsonWebToken(JwtType::REFRESH, $user),
            httpOnly: true
        ));

        return $response;
    }

    #[Route('/registration', name: 'app_registration', methods: [ Request::METHOD_POST ])]
    public function appRegistration(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] 
        RegistrationDto $dto, 
        UserRepository $users
    ): JsonResponse {

        if($users->findOneBy([ 'email' => $dto->email ])) {

            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid credentials!');
        }

        $user = $users->create($dto->email, $dto->password);

        return $this->json([
            'id'    => $user->getId(),
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/refresh-token', name: 'app_refresh_token', methods: [ Request::METHOD_POST ])]
    public function appRefreshToken(
        Request $request, 
        SecurityService $security,
        UserRepository $users
    ): JsonResponse {

        $refreshToken = $request->cookies->get('refreshToken');

        if(!$refreshToken) {

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Missing refreshToken!');
        }

        $jwt = $security->decodeJsonWebToken(JwtType::REFRESH, $refreshToken);

        if(!($user = $users->findOneBy([ 'email' => $jwt->identifier ]))) {

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid refreshToken!');
        }

        return $this->json([
            "accessToken"  => $security->encodeJsonWebToken(JwtType::ACCESS, $user)
        ]);
    }
    
}
