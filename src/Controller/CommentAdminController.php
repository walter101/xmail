<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentAdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/admin/comment", name="comment_admin")
     */
    public function index(Request $request)
    {
        $searQuery = $request->query->get('searchquery');
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $query = $commentRepository->getWithSearchQueryBuilder($searQuery);

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        //$commentRepository = $this->entityManager->getRepository(Comment::class);
//        $comments = $commentRepository->findBy([], [
//            'createdAt' => 'DESC'
//        ]);

        return $this->render('comment_admin/index.html.twig', [
            'pagination' => $pagination,
            'searQuery' => $searQuery
        ]);
    }
}
