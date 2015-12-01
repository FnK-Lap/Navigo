<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Terminal;


class TerminalController extends Controller
{
    /**
     * @Route("/terminals", name="get_terminals", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get all terminals",
     *  output="AppBundle\Entity\Terminal",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getTerminalsAction(Request $request) 
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT t FROM AppBundle:Terminal t";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1) /*page number*/,
            $request->query->getInt('per_page', Terminal::RESULT_PER_PAGE) /*limit per page*/
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

    /**
     * @Route("/terminal/{id}", name="get_terminal", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get terminal by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getTerminalAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $terminal = $em->getRepository('AppBundle:Terminal')->find($id);

        if (!$terminal) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'Terminal not found',
            ));
        }

        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'Success',
            'data'    => $terminal
        ));
    }

    /**
     * @Route("/terminal/{id}", name="delete_terminal", methods="DELETE")
     * 
     * @ApiDoc(
     *  description="Delete terminal by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function deleteTerminalAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $terminal = $em->getRepository('AppBundle:Terminal')->find($id);

        if (!$terminal) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'Terminal not found'
            ));
        }
        $em->remove($terminal);
        $em->flush();

        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'Success'
        ));
    }
}
