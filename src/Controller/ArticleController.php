<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage()
    {
        return new Response('Fecking nice!');
    }

    /**
     * @Route("/news/whyowhy/{slug}")
     * @return Response
     */
    public function show($slug)
    {
        $comments = [
            'Line-1',
            'Line-2',
            'Line-3',
        ];

        return $this->render('article/show.html.twig',
            [
                'title' => ucwords(str_replace('_', ' ', $slug)),
                'comments' => $comments
        ]);
    }
}