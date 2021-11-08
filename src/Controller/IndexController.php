<?php

namespace App\Controller;

use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index(Request $request, FinderInterface $articleFinder)
    {
        $list = $articleFinder->find($request->get('query', ''), 10);
        return $this->render('index.html.twig', [
            'query' => $request->get('query'),
            'list' => $list,
        ]);
    }
}
