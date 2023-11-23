<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private const AJAX_REQUEST_ITEMS_LIMIT = 3;

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    // --------------------------------------------------------

    #[Route('/admin/order/list', name: 'app_admin_order_list')]
    public function list(): Response
    {
        return $this->render('admin/order/list.html.twig', [
            'ajax_request_items_limit' => self::AJAX_REQUEST_ITEMS_LIMIT,
        ]);
    }

    // --------------------------------------------------------

    #[Route('/admin/order/list-ajax', name: 'app_admin_order_list_ajax')]
    public function listAjax(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset');
        $limit = $request->query->getInt('limit');

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('count(o.id)')
            ->from(Order::class, 'o')
        ;

        try {
            $total = $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new \UnexpectedValueException('total result error');
        }

        $queryBuilder = $this->orderRepository->createQueryBuilder('o');
        $orders = $queryBuilder
            ->orderBy('o.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $items = [];
        /** @var Order $order */
        foreach ($orders as $order) {

            $products = [];
            /** @var Product $product */
            foreach ($order->getProducts() as $product) {
                $products[] = $product->getName();
            }

            $items[] = [
                'customer' => $order->getCustomer(),
                'email' => $order->getEmail(),
                'address' => $order->getAddress(),
                'products' => $products,
            ];
        }

        $data = [
            'status' => 'ok',
            'total' => $total,
            'items' => $items,
        ];

        return new JsonResponse($data);
    }
}
