<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Card;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\Payment;

class SubscriptionController extends Controller
{
    /**
     * @Route("/card/{card}/subscription", name="post_subscription", methods="GET")
     */
    public function postSubscriptionAction(Request $request, Card $card)
    {   
        $paypal = $this->get('paypal');
        $paymentDetails = $paypal->request('getExpressCheckoutDetails', array(
            'TOKEN' => $request->query->get('token')
        ));

        if ($paymentDetails['ACK'] == 'Success'
            && $paymentDetails['CHECKOUTSTATUS'] == 'PaymentActionNotInitiated'
            && $paymentDetails['TOKEN'] == $request->query->get('token')
            && $paymentDetails['PAYERID'] == $request->query->get('PayerID')) {

            $duration = $paymentDetails['PAYMENTREQUEST_0_NOTETEXT'];
            $result = $paypal->request('doExpressCheckoutPayment', array(
                'TOKEN'                         => $paymentDetails['TOKEN'],
                'PAYERID'                       => $paymentDetails['PAYERID'],
                'PAYMENTREQUEST_0_AMT'          => $paymentDetails['PAYMENTREQUEST_0_AMT'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR'
            ));

            if ($result['ACK'] == 'Success') {
                $em = $this->get('doctrine.orm.entity_manager');
                $payment = new Payment();
                $payment->setTransactionId($result['PAYMENTINFO_0_TRANSACTIONID'])
                        ->setPaidAt(new \DateTime($result['PAYMENTINFO_0_ORDERTIME']))
                ;
                $em->persist($payment);

                $subscription = new Subscription();
                $subscription->setDuration($duration)
                            ->setSubscribedAt(new \DateTime($result['PAYMENTINFO_0_ORDERTIME']))
                ;
                $em->persist($subscription);

                if ($card->getExpireAt()) {
                    $date = clone $card->getExpireAt();
                    if ($date < new \DateTime()) {
                        $card->setExpireAt(new \DateTime('+1 '.$duration));
                    } else {
                        $card->setExpireAt($date->modify('+1 '.$duration));
                    }
                } else {
                    $card->setExpireAt(new \DateTime('+1 '.$duration));
                }

                $em->flush();

                return new JsonResponse(array(
                    "status"  => 200,
                    "message" => "Success",
                    "data"    => $result
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

    /**
     * @Route("/card/{card}/payment", name="payment", methods="POST")
     */
    public function paymentAction(Request $request, Card $card)
    {
        $data = json_decode($request->getContent(), true);
        $paypal = $this->get('paypal');
        $params = array(
            'RETURNURL' => 'http://navigo.dev/app_dev.php/card/'.$card->getId().'/subscription',
            'CANCELURL' => 'http://navigo.dev/app_dev.php/api/paypal/cancel',

            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
            'L_PAYMENTREQUEST_0_QTY0'  => 1
        );

        
        switch ($data['duration']) {
            case 'week':
                $params['PAYMENTREQUEST_0_AMT']     = 25;
                $params['L_PAYMENTREQUEST_0_NAME0'] = 'Forfait semaine';
                $params['L_PAYMENTREQUEST_0_DESC0'] = 'Un forfait semaine Navigo';
                $params['L_PAYMENTREQUEST_0_AMT0']  = 25;
                $params['PAYMENTREQUEST_0_NOTETEXT'] = 'week';

                break;
            case 'month':
                $params['PAYMENTREQUEST_0_AMT']     = 100;
                $params['L_PAYMENTREQUEST_0_NAME0'] = 'Forfait mois';
                $params['L_PAYMENTREQUEST_0_DESC0'] = 'Un forfait mois Navigo';
                $params['L_PAYMENTREQUEST_0_AMT0']  = 100;
                $params['PAYMENTREQUEST_0_NOTETEXT'] = 'month';

                break;
            default:
                return new JsonResponse(array(
                    'status'  => 400,
                    'message' => 'Failure',
                    'errors'  => "Duration missing (week or month)"
                ));
                break;
        }

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



   
}
