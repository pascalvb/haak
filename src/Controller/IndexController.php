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

        if (!empty($request->get('daterange'))) {
            $dateCutoff = match ($request->get('daterange')) {
               'today' => new \DateTimeImmutable('00:01'),
               '3days' => new \DateTimeImmutable('3 days ago'),
               '7days' => new \DateTimeImmutable('7 days ago'),
               '69days' => new \DateTimeImmutable('69 days ago'),
               '420days' => new \DateTimeImmutable('420 days ago'),
            };

            $query = new Query\BoolQuery();
            $query->addMust(new SimpleQueryString($request->get('query', '')));
            $query->addMust(new Query\Range('createdAt', [
                'gt' => $dateCutoff->format('c'),
            ]));

        } else {
            $query = new SimpleQueryString($request->get('query', ''));
        }

        $query = new Query($query);

        $agg = new \Elastica\Aggregation\Terms('tags');
        $agg->setField('tags.name');
        $query->addAggregation($agg);

        $list = $articleFinder->findPaginated($query, []);
        $list->setMaxPerPage(10);

        $aggregations = $list->getAdapter()->getAggregations();
        return $this->render('index.html.twig', [
            'query' => $request->get('query'),
            'daterange' => $request->get('daterange'),
            'list' => $list->getIterator(),
            'aggregations' => $aggregations,
        ]);
    }
}
