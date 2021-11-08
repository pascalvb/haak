<?php

namespace App\Controller;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    public function index(Request $request, PaginatedFinderInterface $articleFinder)
    {
        $list = $articleFinder->findPaginated($request->get('query', ''), []);
        $list->setMaxPerPage(10);
        return $this->render('index.html.twig', [
            'query' => $request->get('query'),
            'list' => $list->getIterator(),
        ]);
    }
}
