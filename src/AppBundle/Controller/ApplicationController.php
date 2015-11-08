<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApplicationController extends Controller
{
    /**
     * @Route("/paypal", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $paypal = $this->get('paypal');
        $params = array(
            'RETURNURL' => 'http://navigo.dev/app_dev.php/api/paypal/success',
            'CANCELURL' => 'http://navigo.dev/app_dev.php/api/paypal/cancel',

            'PAYMENTREQUEST_0_AMT' => 100,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',

            'L_PAYMENTREQUEST_0_NAME0' => "Une Carte",
            'L_PAYMENTREQUEST_0_DESC0' => "Une carte de transport",
            'L_PAYMENTREQUEST_0_AMT0'  => 100,
            'L_PAYMENTREQUEST_0_QTY0'  => 1
        );

        $result = $paypal->request('SetExpressCheckout', $params);

        if ($result['ACK'] == 'Success') {
            return new JsonResponse(array(
                "status"  => 200,
                "message" => "Success",
                "data"    => array(
                    'paypalRedirectUrl' => $paypal->getPaypalRedirectUrl($result['TOKEN'])
                )
            ));
        } else {
            return new JsonResponse(array(
                'status'  => 400,
                'message' => 'Failure',
                'errors'  => $result
            ));
        }
    }

    /**
     * @Route("/paypal/success", name="paypal_success")
     */
    public function paypalSuccessAction(Request $request)
    {
        $paypal = $this->get('paypal');
        $paymentDetails = $paypal->request('getExpressCheckoutDetails', array(
            'TOKEN' => $request->query->get('token')
        ));

        if ($paymentDetails['ACK'] == 'Success'
            && $paymentDetails['CHECKOUTSTATUS'] == 'PaymentActionNotInitiated'
            && $paymentDetails['TOKEN'] == $request->query->get('token')
            && $paymentDetails['PAYERID'] == $request->query->get('PayerID')) {

            $result = $paypal->request('doExpressCheckoutPayment', array(
                'TOKEN'                         => $paymentDetails['TOKEN'],
                'PAYERID'                       => $paymentDetails['PAYERID'],
                'PAYMENTREQUEST_0_AMT'          => $paymentDetails['PAYMENTREQUEST_0_AMT'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR'
            ));

            if ($result['ACK'] == 'Success') {
                return new JsonResponse(array(
                    "status"  => 200,
                    "message" => "Success",
                    "data"    => array(
                        'data' => $result
                    )
                ));
            } else {
                return new JsonResponse(array(
                    'status'  => 400,
                    'message' => 'Failure',
                    'errors'  => $result
                ));
            }
        } else {
            return new JsonResponse(array(
                'status'  => 400,
                'message' => 'Failure',
                'errors'  => $paymentDetails
            ));
        }
    }
}
