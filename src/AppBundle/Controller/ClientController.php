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

        $clientManager = $this->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://www.example.com'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code', 'client_credentials', 'password'));
        $clientManager->updateClient($client);

        var_dump($client->getPublicId());

        return new JsonResponse(array(
            'status'  => 201,
            'message' => 'Success',
            'errors'  => ''
        ));
    }

   
}
