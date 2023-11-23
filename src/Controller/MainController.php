<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Review;
use App\Form\OrderType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private const AJAX_REQUEST_ITEMS_LIMIT = 3;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ReviewRepository $reviewRepository,
    )
    {
    }

    // --------------------------------------------------------

    #[Route('/main', name: 'app_main')]
    public function index(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            $this->addFlash('success', 'Order created');
        }

        return $this->render('main/index.html.twig', [
            'form' => $form,
            'ajax_request_items_limit' => self::AJAX_REQUEST_ITEMS_LIMIT,
        ]);
    }

    // --------------------------------------------------------

    #[Route('/main/review-ajax', name: 'app_main_review_ajax')]
    public function reviewAjax(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset');
        $limit = $request->query->getInt('limit');

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('count(r.id)')
            ->from(Review::class, 'r')
        ;

        try {
            $total = $queryBuilder
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            throw new \UnexpectedValueException('total result error');
        }

        $queryBuilder = $this->reviewRepository->createQueryBuilder('r');
        $reviews = $queryBuilder
            ->orderBy('r.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $items = [];
        /** @var Review $review */
        foreach ($reviews as $review) {
            $items[] = [
                'created_at' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
                'author' => $review->getAuthor(),
                'message' => $review->getMessage(),
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
