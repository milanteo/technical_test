<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use OpenSSLAsymmetricKey;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Throwable;
use UnexpectedValueException;

class SecurityService {

    const JWT_ALGORITHM = 'RS256';

    public function __construct(
        private ParameterBagInterface $params,
        private RequestStack $stack
    ) { }

    public function getPrivateKey(): OpenSSLAsymmetricKey {

        return openssl_pkey_get_private(file_get_contents($this->params->get('privateKey')));
    }

    public function getPublicKey(): OpenSSLAsymmetricKey {

        return openssl_pkey_get_public(file_get_contents($this->params->get('publicKey')));
    }

    public function decodeJsonWebToken(JwtType $type, string $bearer): object {

        try {

            list($headersB64, $payloadB64, $sig) = explode('.', $bearer);
    
            $payload = json_decode(base64_decode($payloadB64));
    
            if($payload->type !== $type->value) {
    
                throw new UnexpectedValueException();
            }

            return JWT::decode($bearer, new Key($this->getPublicKey(), self::JWT_ALGORITHM));
            
        } catch (ExpiredException $e) {
            
            throw new BadCredentialsException('The provided '. $type->value .' token is expired.');

        } catch(Throwable $e) {

            throw new BadCredentialsException('The provided token is not valid.');

        }

    }

    public function encodeJsonWebToken(JwtType $type, User $user): string {

        $scheme = $this->stack->getMainRequest()->getSchemeAndHttpHost();

        return JWT::encode([
            'type'       => $type->value,
            'identifier' => $user->getUserIdentifier(),
            'iss'        => $scheme,
            'aud'        => $scheme,
            'exp'        => (new DateTime())->modify(match($type) {
                            JwtType::ACCESS  => "+ 15 minutes",
                            JwtType::REFRESH => "+ 24 hours"
                        })->getTimestamp()
        ], $this->getPrivateKey(), self::JWT_ALGORITHM);

    }

}