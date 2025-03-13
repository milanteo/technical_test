<?php

namespace App\Controller;

use App\Dto\CreateOrderDto;
use App\Dto\CreateProductDto;
use App\Dto\PatchOrderDto;
use App\Dto\PatchProductDto;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\QueryDto\GetOrdersDto;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Security\Voter\OrderVoter;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
            ->setFirstResult(($dto->page - 1) * $dto->pageSize)
            ->setMaxResults($dto->pageSize)
        ;

        if(!$this->isGranted('ROLE_ADMIN')) {

            $orders->andWhere($expr->eq('o.createdBy', ':userid'));

            $orders->setParameter('userid', $user->getId());

        }

        if(!!$dto->search) {

            $orders->andWhere($expr->orX(
                $expr->like('LOWER(o.name)',        ':search'),
                $expr->like('LOWER(o.description)', ':search')
            ));

            $search = strtolower($dto->search);

            $orders->setParameter('search', "%{$search}%");

        }

        $paginator = new Paginator($orders);

        $response = $this->json(array_map(
            fn(Order $order) => $api->serializeOrder($order), 
            $paginator->getQuery()->getResult()
        ));

        $response->headers->add([
            'X-Total' => $paginator->count()
        ]);

        return $response;

    } 
    
    #[Route('/orders', name: 'app_post_order', methods: [ Request::METHOD_POST ])]
    public function appPostOrder(
        #[MapRequestPayload()]
        CreateOrderDto $dto,
        #[CurrentUser()]
        User $user,
        OrderRepository $orders,
        EntityManagerInterface $em,
        ApiService $api
    ): JsonResponse {

        $order = $orders->create($user, $api->extractData($dto));

        $em->flush();

        return $this->json($api->serializeOrder($order));
        
    } 

    #[Route('/orders/{order}', name: 'app_patch_order', methods: [ Request::METHOD_PATCH ])]
    #[IsGranted(OrderVoter::EDIT, subject: 'order')]
    public function appPatchOrder(
        #[MapEntity(mapping: [ 'order' => 'id' ])]
        Order $order,
        #[MapRequestPayload()]
        PatchOrderDto $dto,
        EntityManagerInterface $em,
        ApiService $api
    ): JsonResponse {

        $order = $api->update($order, $api->extractData($dto));

        $em->flush();

        return $this->json($api->serializeOrder($order));
        
    } 

    #[Route('/orders/{order}', name: 'app_delete_order', methods: [ Request::METHOD_DELETE ])]
    #[IsGranted(OrderVoter::EDIT, subject: 'order')]
    public function appDeleteOrder(
        #[MapEntity(mapping: [ 'order' => 'id' ])]
        Order $order,
        EntityManagerInterface $em
    ): Response {

        $em->remove($order);
        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
        
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
    
    #[Route('/orders/{order}/products', name: 'app_post_order_product', methods: [ Request::METHOD_POST ])]
    #[IsGranted(OrderVoter::EDIT, subject: 'order')]
    public function appPostOrderProducts(
        #[MapEntity(mapping: [ 'order' => 'id' ])]
        Order $order,
        #[MapRequestPayload()]
        CreateProductDto $dto,
        ProductRepository $products,
        ApiService $api,
        EntityManagerInterface $em
    ): JsonResponse {

        $product = $products->create($order, $api->extractData($dto));

        $em->flush();

        return $this->json($api->serializeProduct($product));
        
    } 
    
    #[Route('/orders/{order}/products/{product}', name: 'app_patch_order_product', methods: [ Request::METHOD_PATCH ])]
    #[IsGranted(OrderVoter::EDIT, subject: 'order')]
    public function appPatchOrderProducts(
        #[MapEntity(mapping: [ 'order' => 'order', 'product' => 'id' ])]
        Product $product,
        PatchProductDto $dto,
        EntityManagerInterface $em,
        ApiService $api
    ): JsonResponse {

        $product = $api->update($product, $api->extractData($dto));

        $em->flush();

        return $this->json($api->serializeProduct($product));
        
    } 

    #[Route('/orders/{order}/products/{product}', name: 'app_delete_order_product', methods: [ Request::METHOD_DELETE ])]
    #[IsGranted(OrderVoter::EDIT, subject: 'order')]
    public function appDeleteOrderProducts(
        #[MapEntity(mapping: [ 'order' => 'order', 'product' => 'id' ])]
        Product $product,
        EntityManagerInterface $em
    ): Response {

        $em->remove($product);
        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
        
    } 
}
