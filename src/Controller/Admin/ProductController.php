<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private const AJAX_REQUEST_ITEMS_LIMIT = 3;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    // --------------------------------------------------------

    #[Route('/admin/product/list', name: 'app_admin_product_list')]
    public function list(): Response
    {
        return $this->render('admin/product/list.html.twig', [
            'ajax_request_items_limit' => self::AJAX_REQUEST_ITEMS_LIMIT,
        ]);
    }

    // --------------------------------------------------------

    #[Route('/admin/product/list-ajax', name: 'app_admin_product_list_ajax')]
    public function listAjax(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset');
        $limit = $request->query->getInt('limit');

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('count(product.id)')
            ->from(Product::class, 'product')
            ;

        try {
            $total = $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new \UnexpectedValueException('total result error');
        }

        $queryBuilder = $this->productRepository->createQueryBuilder('p');
        $products = $queryBuilder
            ->orderBy('p.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'images' => $product->getImages(),
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
