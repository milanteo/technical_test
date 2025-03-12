<?php

namespace App\Service;

use App\Dto\CreateOrderDto;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Ds\Map;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;
use ReflectionClass;
use stdClass;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ApiService {

    private Serializer $serializer;

    public function __construct(private EntityManagerInterface $em) {

        $this->serializer = new Serializer([ 
            new DateTimeNormalizer([ 
                DateTimeNormalizer::FORMAT_KEY => 'd-m-Y' 
            ]), 
            new ObjectNormalizer() 
        ], [ 
            new JsonEncoder() 
        ]);

    }

    public function extractData(mixed $dto): Map {

        $resolver = TypeResolver::create();

        $reflection = new ReflectionClass($dto);

        $properties = array_filter($reflection->getProperties(), fn($p) => $p->isInitialized($dto));

        $data = new Map();

        $parseValue = function(Type $docType, mixed $value) {

            if($docType instanceof ObjectType) {

                return $this->extractData($value);
            }

            return $value;

        };

        foreach ($properties as $p) {

            $docType = $resolver->resolve($p);

            if($docType instanceof CollectionType) {

                $data->put($p->getName(), array_map(fn($v) => $parseValue($docType->getCollectionValueType(), $v), $dto->{$p->getName()}));

            } else {

                $data->put($p->getName(), $parseValue($docType, $dto->{$p->getName()}));

            }

        }

        return $data;

    }

    /**
     * @template T
     * @param T $object
     * @param Map $dto
     * @return T
     */
    public function update(object $object, Map $dto) {

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($dto as $key => $value) {
            
            $accessor->setValue($object, $key, $value);
        }

        $this->em->persist($object);

        return $object;
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