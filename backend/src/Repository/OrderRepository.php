<?php

namespace App\Repository;

use App\Dto\CreateOrderDto;
use App\Entity\Order;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ds\Map;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ProductRepository $products)
    {
        parent::__construct($registry, Order::class);
    }

    public function create(User $user, Map $dto): Order {

        $order = new Order();

        $order->setName($dto->get('name'));

        $order->setDescription($dto->get('description', null));

        $order->setDate(new DateTime());

        $order->setCreatedBy($user);

        foreach ($dto->get('products', []) as $productDto) {
            
            $product = $this->products->create($order, $productDto);

            $order->addProduct($product);

        }

        $this->getEntityManager()->persist($order);

        return $order;

    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
