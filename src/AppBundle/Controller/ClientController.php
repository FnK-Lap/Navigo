<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends Controller
{
    /**
     * @Route("/client", name="post_client", methods="POST")
     */
    public function postClientAction(Request $request)
    {
        if (empty($request->query->get('redirect_uri'))) {
            return new JsonResponse(array(
                'status'  => 400,
                'message' => 'Fail',
                'errors'  => 'Missing parameter redirect_uri'
            ), 400);
        }

        $clientManager = $this->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array($request->query->get('redirect_uri')));
        $client->setAllowedGrantTypes(array('token', 'authorization_code'));
        $clientManager->updateClient($client);

        return new JsonResponse(array(
            'status'  => 201,
            'message' => 'Success',
            'errors'  => '',
            'client'  => array(
                'client_id'     => $client->getPublicId(),
                'client_secret' => $client->getSecret()
            )
        ));
    }

   
}
