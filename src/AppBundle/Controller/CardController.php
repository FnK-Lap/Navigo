<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Card;
use AppBundle\Form\CardType;

/**
 * @Route("/api")
 */
class CardController extends Controller
{
    /**
     * @Route("/cards", name="post_cards", methods="POST")
     *
     * @ApiDoc(
     *  description="Post a new Card",
     *  statusCodes={
     *          201: "Created"
     *    }
     * )
     */
    public function postCardAction(Request $request)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $card = new Card();

        $form = $this->createForm(new CardType(), $card);
        $form->submit($data);



        
        if ($form->isValid()) {
            $card->setUser($user);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($card);
            $em->flush();

            return new JsonResponse(array(
                'status' => 201,
                'message' => 'Card created with success',
                'data' => $card 
            ));
        } 

        return new JsonResponse(array(
            'status' => 400,
            'message' => 'Fail',
            'error' => $form->getErrorsAsString()
        ), 400);
    }

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

    /**
     * @Route("/card/{id}", name="get_card", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get card by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getCardAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $card = $em->getRepository('AppBundle:Card')->find($id);

        if (!$card) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'Card not found',
            ));
        } 

        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'Success',
            'data'    => $card
        ));
    }

    /**
     * @Route("/card/{id}", name="delete_card", methods="DELETE")
     * 
     * @ApiDoc(
     *  description="Delete card by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function deleteCardAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $card = $em->getRepository('AppBundle:Card')->find($id);

        if (!$card) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'Card not found'
            ));
        }
        $em->remove($card);
        $em->flush();

        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'Success'
        ));
    }

    /**
     * @Route("/card/validate", name="validate_card", methods="POST")
     *
     */
    public function validateCardAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $data = json_decode($request->getContent(), true);

        if (!isset($data["serial_number"])) {
            return new JsonResponse(array(
                'status'  => 400,
                'message' => 'Fail',
                'error'   => 'Missing serial_number parameter'
            ), 400);
        }

        $sn = $data["serial_number"];
        $card = $em->getRepository('AppBundle:Card')->findOneBy(array('serialNumber' => $sn));

        if (!$card) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'Card not found'
            ));
        }

        if (!$card->getIsActive()) {
            return new JsonResponse(array(
                'status' => 400,
                'message' => 'Card not active'
            ));
        }

        if ($card->getExpireAt() < new \DateTime()) {
            return new JsonResponse(array(
                'status' => 400,
                'message' => 'Card expired'
            ));
        }

       

    }

    
}
