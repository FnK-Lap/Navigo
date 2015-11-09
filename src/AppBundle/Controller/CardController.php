<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Card;


class CardController extends Controller
{
    /**
     * @Route("/cards", name="get_cards", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get all cards",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getCardsAction(Request $request) 
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT c FROM AppBundle:Card c";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1) /*page number*/,
            $request->query->getInt('per_page', Card::RESULT_PER_PAGE) /*limit per page*/
        );

        return new JsonResponse(array(
            "status"  => 200,
            "message" => "Success",
            "data"    => array(
                'items'        => $pagination->getItems(),
                'total'        => $pagination->getPaginationData()['totalCount'],
                'params'       => $pagination->getParams(),
                'pagination'   => array(
                    'totalPages'   => $pagination->getPaginationData()['pageCount'],
                    'itemsPerPage' => $pagination->getPaginationData()['numItemsPerPage'],
                    'currentPage'  => $pagination->getPaginationData()['current'],
                    'pagesInRange' => $pagination->getPaginationData()['pagesInRange'],
                    'pageRange'    => $pagination->getPaginationData()['pageRange']
                )
            )
        ));
    }
}
