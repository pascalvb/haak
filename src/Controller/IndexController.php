<?php

namespace App\Controller;

use Elastica\Query;
use Elastica\Query\SimpleQueryString;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    public function index(Request $request, PaginatedFinderInterface $articleFinder)
    {
        $query = new Query(new SimpleQueryString($request->get('query', '')));
        $agg = new \Elastica\Aggregation\Terms('tags');
        $agg->setField('tags.name');
        $query->addAggregation($agg);

        $list = $articleFinder->findPaginated($query, []);
        $list->setMaxPerPage(10);

        $aggregations = $list->getAdapter()->getAggregations();
        return $this->render('index.html.twig', [
            'query' => $request->get('query'),
            'list' => $list->getIterator(),
            'aggregations' => $aggregations,
        ]);
    }
}
