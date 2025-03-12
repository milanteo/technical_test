<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiService {

    private Serializer $serializer;

    public function __construct() {

        $this->serializer = new Serializer([ new ObjectNormalizer() ], [ new JsonEncoder() ]);

    }

    public function serializeOrder(Order $order): array {

        return $this->serializer->normalize(data: $order, context: [
            AbstractObjectNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'description',
                'date'
            ]
        ]);
    }

    public function serializeProduct(Product $product): array {

        return $this->serializer->normalize(data: $product, context: [
            AbstractObjectNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'price'
            ]
        ]);
    }

}