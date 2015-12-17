<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;


class UserController extends Controller
{
    /**
     * @Route("/users", name="get_users", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get all users",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getUsersAction(Request $request) 
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT u FROM AppBundle:User u";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1) /*page number*/,
            $request->query->getInt('per_page', User::RESULT_PER_PAGE) /*limit per page*/
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
     * @Route("/user/{id}", name="get_user", methods="GET")
     * 
     * @ApiDoc(
     *  description="Get user by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function getUserAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->find($id);

        if (!$user) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'User not found',
            ));
        }
            
        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'Success',
            'data'    => $user
        ));
    }

    /**
     * @Route("/user/{id}", name="delete_user", methods="DELETE")
     * 
     * @ApiDoc(
     *  description="Delete user by id",
     *  statusCodes={
     *         200: "good"
     *     }
     * )
     */
    public function deleteUserAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->find($id);

        if (!$user) {
            return new JsonResponse(array(
                'status'  => 404,
                'message' => 'User not found'
            ));
        }
        $em->remove($user);
        $em->flush();

        return new JsonResponse(array(
            'status'  => 200,
            'message' => 'User deleted'
        ));
    }

    /**
     * @Route("/register", name="register", methods="POST")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $user->setCreatedAt(new \Datetime());
        $user->setUpdatedAt(new \Datetime());
        $form = $this->createForm(new UserType(), $user);

        // 2) handle the submit (will only happen on POST)
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like send them an email, etc
            // maybe set a "flash" success message for the user
            die('registered');
            return $this->redirectToRoute('replace_with_some_route');
        }

        return new JsonResponse(array(
            'status' => 400,
            'message' => $form->getErrorsAsString()
        ));
    }

    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
    }

    // /**
    //  * @Route("/login_check", name="login_check")
    //  */
    // public function loginCheckAction()
    // {
    //     // this controller will not be executed,
    //     // as the route is handled by the Security system
    // }
}
