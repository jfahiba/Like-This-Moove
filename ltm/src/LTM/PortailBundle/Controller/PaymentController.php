<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 27/07/2014
 * Time: 21:06
 */

namespace LTM\PortailBundle\Controller;


use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Registry\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Payum\Bundle\PayumBundle\Controller\PayumController as ParentController; 
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Payum\Core\Request\BinaryMaskStatusRequest;
use Symfony\Component\Validator\Constraints\Range;

class PaymentController extends ParentController
{
    public function preparePaypalExpressCheckoutPaymentAction(Request $request)
    {
        $paymentName = 'ltm_credit_payment';

        $form = $this->createPurchaseForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
		
            $storage = $this->getPayum()->getStorage('LTM\PortailBundle\Entity\PaymentDetails');

            /** @var $paymentDetails PaymentDetails */
            $paymentDetails = $storage->createModel();
            $currency = $data['currency'];
            $quantity = $data['amount'];
            $paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = $currency;
            $paymentDetails['PAYMENTREQUEST_0_AMT'] = $quantity;
            $paymentDetails['NOSHIPPING'] = Api::NOSHIPPING_NOT_DISPLAY_ADDRESS;
            $paymentDetails['REQCONFIRMSHIPPING'] = Api::REQCONFIRMSHIPPING_NOT_REQUIRED;
            //$paymentDetails['L_PAYMENTREQUEST_0_ITEMCATEGORY0'] = Api::PAYMENTREQUEST_ITERMCATEGORY_DIGITAL;
            $paymentDetails['L_PAYMENTREQUEST_0_AMT0'] = $quantity;
            $paymentDetails['L_PAYMENTREQUEST_0_QTY0'] = 1;
            $paymentDetails['L_PAYMENTREQUEST_0_NAME0'] = 'LikeThisMoove.com';
			
            try{
                $quantity = ($currency != 'CAD' ? $this->exchangeRate($quantity, $currency , 'CAD') : $quantity);
            }catch (Exception $e){

            }
            $paymentDetails['L_PAYMENTREQUEST_0_DESC0'] = ($quantity * 10) . ' credits video';
            $storage->updateModel($paymentDetails);

            $captureToken = $this->getTokenFactory()->createCaptureToken(
                $paymentName,
                $paymentDetails,
                'ltm_payment_details_voir'

            );
 
            $paymentDetails['RETURNURL'] = $captureToken->getTargetUrl();
            $paymentDetails['CANCELURL'] = $captureToken->getTargetUrl();
            $paymentDetails['INVNUM'] = $paymentDetails->getId();

			
		
            $storage->updateModel($paymentDetails);

			
            return $this->redirect($captureToken->getTargetUrl());
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:Payment:editer.html.twig',
            array(
                'form' => $form->createView(),
                'paymentName' => $paymentName,
                'errors' => '',
                'cadCredit' => 10,
                'usdCredit' => $this->exchangeRate(10, 'USD' , 'CAD'),
                'eurCredit' => $this->exchangeRate(10, 'EUR' , 'CAD'),
                'tuto' => true

            ));

    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createPurchaseForm()
    {
        return $this->createFormBuilder()
            ->add('amount', 'choice',
                array(
                    'choices'   => array('5' => '5', '10' => '10',  '15' => '15', '20' => '20'), 'label' => 'Montant', 'required' => true))
            /*->add('amount', null, array(
                'label' => 'Montant',
                'data' => 5,
                'required' => true,
                'constraints' => array(new Range(array('max' => 20)))
            ))*/
            /*->add('currency',
                null,
                array(
                    'label' => 'Devise',
                    'data' => 'CAD'))*/
            ->add('currency', 'choice',
            array(
                'choices'   => array('CAD' => 'CAD', 'USD' => 'USD',  'EUR' => 'EUR'), 'label' => 'Devise', 'required' => true))
            ->getForm()
            ;
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    public function captureDoneAction(Request $request)
    {
		
        $token = $this->get('payum.security.http_request_verifier')->verify($request);


        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);


        if ($status->isSuccess()) {


            $em = $this->container->get('doctrine')->getManager();
            $context = $this->get('security.context');
            $currentUser = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

            $quantity = $status->getModel()['PAYMENTREQUEST_0_AMT'];
            $currency = $status->getModel()['PAYMENTREQUEST_0_CURRENCYCODE'];

            try{
                $quantity = ($currency != 'CAD' ? $this->exchangeRate($quantity, $currency , 'CAD') : $quantity);
            }catch (Exception $e){

            }
            $currentUser->buyCredits($quantity * 10, $em);

            $this->get('session')->getFlashBag()->set(
                'notice',
                'Payment success. Credits were added'
            );
        } else if ($status->isPending()) {
            $this->get('session')->getFlashBag()->set(
                'notice',
                'Payment is still pending. Credits were not added'
            );
        } else {
            $this->get('session')->getFlashBag()->set('error', 'Payment failed');
        }

        return $this->redirect($this->generateUrl('ltm_usr_credit_historique'));
        /*return $this->render('PortailBundle:Payment:viewdetails.html.twig', array(
            'status' => $status,
            'paymentTitle' => ucwords(str_replace(array('_', '-'), ' ', $token->getPaymentName()))
        ));*/
    }

    function exchangeRate( $amount, $currency, $exchangeIn )
    {
        $from = $currency;
        $to = $exchangeIn;
        $url = 'http://finance.yahoo.com/d/quotes.csv?f=l1d1t1&s='.$from.$to.'=X';
        $handle = fopen($url, 'r');

        if ($handle) {
            $result = fgetcsv($handle);
            fclose($handle);
        }

        return $result[0]*$amount;
    }

    public function pricingAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $context = $this->get('security.context');
		$user = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        if (!$user)
        {
            throw new NotFoundHttpException("Utilisateur non trouvé");

        }

        return $this->render('PortailBundle:Payment:pricing.html.twig', array('user' => $user)
       );
    }

}