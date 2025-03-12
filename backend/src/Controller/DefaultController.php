<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\QueryDto\GetOrdersDto;
use App\Security\Voter\OrderVoter;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1', format: 'json')]
final class DefaultController extends AbstractController {

    #[Route('/orders', name: 'app_get_orders', methods: [ Request::METHOD_GET ])]
    public function appGetOrders(
        EntityManagerInterface $em, 
        #[MapQueryString()] 
        GetOrdersDto $dto,
        #[CurrentUser()]
        User $user,
        ApiService $api
    ): JsonResponse {

        $expr = $em->getExpressionBuilder();

        $orders = $em->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->orderBy("o.{$dto->orderBy}", $dto->orderDir)
        ;

        if(!$this->isGranted('ROLE_ADMIN')) {

            $orders->andWhere($expr->eq('o.createdBy', ':userid'));

            $orders->setParameter('userid', $user->getId());

        }

        if(!!$dto->search) {

            $orders->andWhere($expr->orX(
                $expr->like('o.code',        ':search'),
                $expr->like('o.description', ':search')
            ));

            $orders->setParameter('search', "%{$dto->search}%");

        }

        return $this->json(array_map(
            fn(Order $order) => $api->serializeOrder($order), 
            $orders->getQuery()->getResult()
        ));

    }      

    #[Route('/orders/{order}/products', name: 'app_get_order_products', methods: [ Request::METHOD_GET ])]
    #[IsGranted(OrderVoter::VIEW, subject: 'order')]
    public function appGetOrderProducts(
        #[MapEntity(mapping: [ 'order' => 'id' ])]
        Order $order,
        ApiService $api
    ): JsonResponse {

        return $this->json(array_map(
            fn(Product $product) => $api->serializeProduct($product), 
            $order->getProducts()->toArray()
        ));
        
    } 
}
