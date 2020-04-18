<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleAdminControlle extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/article/new")
     */
    public function new()
    {
        $article = new Article();
        $article->setContent('Some new articles great new content');
        $article->setSlug('slug-'.rand(1,1000));
        $article->setTitle('title');
        $article->setAuthor('Walter Pothof');
        $article->setHeartCount(rand(1,1000));
        $article->setImageFilename('lightspeed.png');
        $daysOld = rand(1, 31);
        $article->setPublishedAt(new \DateTime(sprintf('-%d days', $daysOld)));

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new Response('some new route');
    }
}