<?php
/**
 * Created by PhpStorm.
 * User: jfahiba
 * Date: 27/07/2014
 * Time: 21:06
 */

namespace LTM\PortailBundle\Controller;


use LTM\PortailBundle\Model\AgreementDetails;
use LTM\PortailBundle\Model\RecurringPaymentDetails;
use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfileRequest;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\ManageRecurringPaymentsProfileStatusRequest;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Core\Request\SyncRequest;
use Payum\Core\Request\BinaryMaskStatusRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RecurringPaymentController extends PayumController
{

    protected function getAboInfo($moovertype, $accounttype, $frequency, $duration) {

        // TODO: use moovertype to check frequencues and durations
		switch($frequency) {
            case 'freq1':
                $freq = 1;
                break;
            case 'freq3':
                $freq = 3;
                break;
            case 'freq6':
                $freq = 6;
                break;
			case 'freq12':
				$freq = 12;
				break;
            default:
                $freq = 12;
        }
        // Abonnement period
		
        switch ($duration) {
            case 'duration1':
                $period = 'P1M';
                break;
			case 'duration3':
                $period = 'P3M';
                break;
            case 'duration6':
                $period = 'P6M';
                break;
            case 'duration12':
                $period = 'P12M';
                break;
            default:
                $period = 'P12M';
                break;
        }

        switch ($accounttype) {

            case 'min':
                $name = 'Semi Pro';
                $price = $this->get('translator')->trans('forfaits.price.' . $moovertype .'.min');
                break;
            case 'avg':
                $name = 'Semi Pro';
                $price = $this->get('translator')->trans('forfaits.price.' . $moovertype .'.avg');
                break;
            case 'max':;
                $name = 'Pro';
                $price = $this->get('translator')->trans('forfaits.price.' . $moovertype .'.max');
                break;
            case 'Visiteur':;
                $name = 'Visiteur';
                $price = 0;
                break;
            case 'Amateur':;
                $name = 'Amateur';
                $price = 0;
                break;
            default:
                $name = 'Visiteur';
                $price = 0;
                break;

        }

        return array(
            'aboname'=> $name,
            'moovertype' => $moovertype,
            'accounttype' => $accounttype,
            'price'=> $price,
            'frequency' => $freq,
            'period' => $period) ;
    }

    /**
     * @return array
     */
    protected function getLTMSubscriptionDetails($aboInfo)
    {

        return array(
            'description' =>
                $this->get('translator')->trans("abo.ltm") . ' ' .
                $aboInfo['moovertype']. ' ' .
                $aboInfo['aboname']. ' - '.
                $aboInfo['price'].
                $this->get('translator')->trans("cad.month").
                ' (' . $this->get('translator')->trans("pay").' ' .
                ($aboInfo['price'] * $aboInfo['frequency']). ' CAD/'.
                ($aboInfo['frequency'] == 3 ? $this->get('translator')->trans('Trimester') : (
                $aboInfo['frequency'] == 6 ? $this->get('translator')->trans('Semester') : (
                $aboInfo['frequency'] == 12 ? $this->get('translator')->trans('Year') :
                    $this->get('translator')->trans('Month')
                )
                )) . ')'
            ,
            'price' => $aboInfo['price'],
            'currency' => 'CAD',
            'frequency' => $aboInfo['frequency'],
            'period' => $aboInfo['period']
        );
    }

    public function createAgreementAction(Request $request, $moovertype, $accounttype, $duration, $frequency)
    {
		
        $aboInfo = $this->getAboInfo($moovertype, $accounttype, $frequency, $duration) ;


		if ($accounttype == 'Amateur' or $accounttype == 'Visiteur') {
		
			$context = $this->get('security.context');
			$em = $this->container->get('doctrine')->getManager();
			$currentUser = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

			$startDate = new \DateTime("now");
            $startDateTxt = $startDate->format("Y-m-d\TH:i:s\Z");
			$endDate = $startDate->add(new \DateInterval($aboInfo['period'])) ;
            $endDateTxt = $endDate->format("Y-m-d\TH:i:s\Z");
			$currentUser->updateAbonnement(
                $aboInfo['aboname'],
                'dancer',
                $aboInfo['aboname'],
                null,
                0,
                'CAD' ,
                null,
                $startDateTxt,
                $endDateTxt,
                "Active",
                null,
                $this->get('translator')->trans("abo.ltm") . ' ' .
                $aboInfo['moovertype']. ' ' .
                $aboInfo['aboname']. ' - 0 ' .
                $this->get('translator')->trans("cad.month"),
                null,
                $em);

			return $this->redirect($this->generateUrl('ltm_payment_credit_pricing'
			));
		}
		
        $paymentName = 'paypal_express_checkout_recurring_payment_and_doctrine_orm';

        $subscription = $this->getLTMSubscriptionDetails($aboInfo);

        if ($request->isMethod('POST')) {
            $storage = $this->getPayum()->getStorage('LTM\PortailBundle\Entity\AgreementDetails');

            /** @var $agreement AgreementDetails */
            $agreement = $storage->createModel();
            $agreement['PAYMENTREQUEST_0_AMT'] = 0;
            $agreement['L_BILLINGTYPE0'] = Api::BILLINGTYPE_RECURRING_PAYMENTS;
            $agreement['L_BILLINGAGREEMENTDESCRIPTION0'] = $subscription['description'];
            $agreement['NOSHIPPING'] = 1;
            $storage->updateModel($agreement);

            $captureToken = $this->getTokenFactory()->createCaptureToken(
                $paymentName,
                $agreement,
                'ltm_paypal_express_checkout_create_recurring_payment',
                array('moovertype' => $moovertype, 'accounttype' => $accounttype, 'frequency' => $frequency, 'duration' => $duration)
            );

            $agreement['RETURNURL'] = $captureToken->getTargetUrl();
            $agreement['CANCELURL'] = $captureToken->getTargetUrl();
            $agreement['INVNUM'] = $agreement->getId();
            $storage->updateModel($agreement);

            return $this->redirect($captureToken->getTargetUrl());
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:RecurringPayment:createAgreement.html.twig',
            array(
                'subscription' => $subscription,
                'paymentName' => $paymentName,
                'errors' => '',
                'frequencytext' => $aboInfo['frequency'] == 3 ? 'Trimester' : (
                    $aboInfo['frequency'] == 6 ? 'Semester' : (
                    $aboInfo['frequency'] == 12 ? 'Year' : 'Month'
                    )
                ),
                'period' => $duration,
                'cadCredit' => $aboInfo['price'] * $aboInfo['frequency'],
                'usdCredit' => $this->exchangeRate($aboInfo['price'], 'CAD' , 'USD') * $aboInfo['frequency'],
                'eurCredit' => $this->exchangeRate($aboInfo['price'], 'CAD' , 'EUR') * $aboInfo['frequency'],


            ));
    }

    public function createRecurringPaymentAction($moovertype, $accounttype, $frequency, $duration, Request $request)
    {

        $aboInfo = $this->getAboInfo($moovertype, $accounttype, $frequency, $duration) ;

        $token = $this->getHttpRequestVerifier()->verify($request);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $agreementStatus = new BinaryMaskStatusRequest($token);
        $payment->execute($agreementStatus);

        $recurringPaymentStatus = null;
        if (false == $agreementStatus->isSuccess()) {
            throw new HttpException(400, 'Billing agreement status is not success.');
        }

        $subscription = $this->getLTMSubscriptionDetails($aboInfo);
        $agreement = $agreementStatus->getModel();

        $storage = $this->getPayum()->getStorage('LTM\PortailBundle\Entity\RecurringPaymentDetails');

        $paymentDetails = $storage->createModel();
        $paymentDetails['TOKEN'] = $agreement['TOKEN'];
        $paymentDetails['DESC'] = $agreement['L_BILLINGAGREEMENTDESCRIPTION0'];
        $paymentDetails['EMAIL'] = $agreement['EMAIL'];
        $paymentDetails['AMT'] = $subscription['price'] * $subscription['frequency'];
        $paymentDetails['CURRENCYCODE'] = $subscription['currency'];
        $paymentDetails['BILLINGFREQUENCY'] = $subscription['frequency'];
        $startDate = new \DateTime("now");
        $startDateTxt = $startDate->format("Y-m-d\TH:i:s\Z");
        $endDate = $startDate->add(new \DateInterval($aboInfo['period'])) ;
        $endDateTxt = $endDate->format("Y-m-d\TH:i:s\Z");
        $paymentDetails['PROFILESTARTDATE'] = $startDateTxt;
        $paymentDetails['FINALPAYMENTDUEDATE'] = $endDateTxt;
        $paymentDetails['BILLINGPERIOD'] = Api::BILLINGPERIOD_MONTH;
        $paymentDetails['AUTOBILLOUTAMT'] = Api::AUTOBILLOUTAMT_ADDTONEXTBILLING;



        //$paymentDetails['INITAMT'] = 1;


        $paymentDetails['NOSHIPPING'] = Api::NOSHIPPING_NOT_DISPLAY_ADDRESS;
        $paymentDetails['REQCONFIRMSHIPPING'] = Api::REQCONFIRMSHIPPING_NOT_REQUIRED;
        //$paymentDetails['L_PAYMENTREQUEST_0_ITEMCATEGORY0'] = Api::PAYMENTREQUEST_ITERMCATEGORY_DIGITAL;
        $paymentDetails['L_PAYMENTREQUEST_0_AMT0'] = $subscription['price'];
        $paymentDetails['L_PAYMENTREQUEST_0_QTY0'] = $subscription['frequency'];
        $paymentDetails['L_PAYMENTREQUEST_0_NAME0'] = 'LikeThisMoove.com';


        $payment->execute(new CreateRecurringPaymentProfileRequest($paymentDetails));
        $payment->execute(new SyncRequest($paymentDetails));

        $recurringPaymentStatus = new BinaryMaskStatusRequest($paymentDetails);
        $payment->execute($recurringPaymentStatus);


        $em = $this->container->get('doctrine')->getManager();
		
		$context = $this->get('security.context');
		
        $currentUser = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());

        // Parse status
        if ($recurringPaymentStatus->isSuccess()) {



            $aboprice = $recurringPaymentStatus->getModel()['AMT'];
            $abocurrency = $recurringPaymentStatus->getModel()['CURRENCYCODE'];
            $abofrequency = $recurringPaymentStatus->getModel()['BILLINGFREQUENCY'];
            $abostartDate = $recurringPaymentStatus->getModel()['PROFILESTARTDATE'];
            $startDate = new \DateTime("now");
            $endDate = $startDate->add(new \DateInterval($subscription['period']));
            //$aboendDate = $recurringPaymentStatus->getModel()['FINALPAYMENTDUEDATE'];
            $aboendDate = $endDate->format("Y-m-d\TH:i:s\Z");
            $aboStatus = $recurringPaymentStatus->getModel()['STATUS']; // "Active"
            $aboToken = $recurringPaymentStatus->getModel()['TOKEN'];
            $aboDesc = $recurringPaymentStatus->getModel()['DESC'];
            $aboPeriod = $recurringPaymentStatus->getModel()['BILLINGPERIOD'];


            $cancelToken = $this->getTokenFactory()->createToken(
                $token->getPaymentName(),
                $paymentDetails,
                'ltm_paypal_express_checkout_cancel_recurring_payment',
                array(),
                $request->attributes->get('_route'),
                $request->attributes->get('_route_params')
            );

            $currentUser->updateAbonnement(
                $aboInfo['aboname'],
                $aboInfo['moovertype'],
                $aboInfo['accounttype'],
                $cancelToken->getHash(),
                $aboprice,
                $abocurrency ,
                $abofrequency,
                $abostartDate,
                $aboendDate,
                $aboStatus,
                $aboToken,
                $aboDesc,
                $aboPeriod,
                $em);
        }


        //return $this->redirect($this->generateUrl('ltm_usr_credit_historique'));

        return $this->redirect($this->generateUrl('ltm_payment_credit_pricing', array(
            'idUser' => $currentUser->getId(),

        )));

        /*return $this->redirect($this->generateUrl('ltm_paypal_express_checkout_view_recurring_payment', array(
            'paymentName' => $token->getPaymentName(),
            'billingAgreementId' => $agreement->getId(),
            'recurringPaymentId' => $paymentDetails->getId(),
        )));*/
    }


    public function viewRecurringPaymentDetailsAction($paymentName, $billingAgreementId, $recurringPaymentId, Request $request)
    {
        $payment = $this->getPayum()->getPayment($paymentName);

        $billingAgreementStorage = $this->getPayum()->getStorage('LTM\PortailBundle\Entity\AgreementDetails');

        $billingAgreementDetails = $billingAgreementStorage->findModelById($billingAgreementId);

        $billingAgreementStatus = new BinaryMaskStatusRequest($billingAgreementDetails);
        $payment->execute($billingAgreementStatus);

        $recurringPaymentStorage = $this->getPayum()->getStorage('LTM\PortailBundle\Entity\RecurringPaymentDetails');

        $recurringPaymentDetails = $recurringPaymentStorage->findModelById($recurringPaymentId);

        $recurringPaymentStatus = new BinaryMaskStatusRequest($recurringPaymentDetails);
        $payment->execute($recurringPaymentStatus);

        $cancelToken = null;
        if ($recurringPaymentStatus->isSuccess()) {
            $cancelToken = $this->getTokenFactory()->createToken(
                $paymentName,
                $recurringPaymentDetails,
                'ltm_paypal_express_checkout_cancel_recurring_payment',
                array(),
                $request->attributes->get('_route'),
                $request->attributes->get('_route_params')
            );
        }

        return $this->container->get('templating')->renderResponse(
            'PortailBundle:RecurringPayment:viewRecurringPaymentDetails.html.twig',
            array(
                'cancelToken' => $cancelToken,
                'billingAgreementStatus' => $billingAgreementStatus,
                'recurringPaymentStatus' => $recurringPaymentStatus,
                'paymentName' => $paymentName,
                'errors' => '',
                'cadCredit' => 10,
                'usdCredit' => $this->exchangeRate(10, 'USD' , 'CAD'),
                'eurCredit' => $this->exchangeRate(10, 'EUR' , 'CAD')
            ));
    }


    public function cancelRecurringPaymentAction(Request $request)
    {
        $token = $this->getHttpRequestVerifier()->verify($request);
        $this->getHttpRequestVerifier()->invalidate($token);

        $payment = $this->getPayum()->getPayment($token->getPaymentName());

        $status = new BinaryMaskStatusRequest($token);
        $payment->execute($status);
        if (false == $status->isSuccess()) {
            throw new HttpException(400, 'The model status must be success.');
        }
        if (false == $status->getModel() instanceof RecurringPaymentDetails) {
            throw new HttpException(400, 'The model associated with token not a recurring payment one.');
        }

        /** @var RecurringPaymentDetails $recurringPayment */
        $paymentDetails = $status->getModel();
        $paymentDetails['ACTION'] = Api::RECURRINGPAYMENTACTION_CANCEL;

        $payment->execute(new ManageRecurringPaymentsProfileStatusRequest($paymentDetails));
        $payment->execute(new SyncRequest($paymentDetails));

        $recurringPaymentStatus = new BinaryMaskStatusRequest($paymentDetails);
        $payment->execute($recurringPaymentStatus);

        if ($status->isSuccess()) {
            $aboprice = $recurringPaymentStatus->getModel()['AMT'];
            $abocurrency = $recurringPaymentStatus->getModel()['CURRENCYCODE'];
            $abofrequency = $recurringPaymentStatus->getModel()['BILLINGFREQUENCY'];
            $abostartDate = $recurringPaymentStatus->getModel()['PROFILESTARTDATE'];
            $aboendDate = $recurringPaymentStatus->getModel()['FINALPAYMENTDUEDATE'];
            $aboStatus = $recurringPaymentStatus->getModel()['STATUS']; // "Active"
            $aboToken = $recurringPaymentStatus->getModel()['TOKEN'];
            $aboDesc = $recurringPaymentStatus->getModel()['DESC'];
            $aboPeriod = $recurringPaymentStatus->getModel()['BILLINGPERIOD'];


            $cancelToken = $this->getTokenFactory()->createToken(
                $token->getPaymentName(),
                $paymentDetails,
                'ltm_paypal_express_checkout_cancel_recurring_payment',
                array(),
                $request->attributes->get('_route'),
                $request->attributes->get('_route_params')
            );



            $em = $this->container->get('doctrine')->getManager();
            $context = $this->get('security.context');
            $currentUser = $em->find('PortailBundle:User', $context->getToken()->getUser()->getId());


            $currentUser->updateAbonnement(
                'Pro (Essai gratuit 14 jours)',
                'dancer',
                'Amateur',
                $cancelToken->getHash(),
                $aboprice,
                $abocurrency ,
                $abofrequency,
                $abostartDate,
                $aboendDate,
                $aboStatus,
                $aboToken,
                $aboDesc,
                $aboPeriod,
                $em);
        }

        return $this->redirect($token->getAfterUrl());
    }


    /**
     * @return GenericTokenFactoryInterface
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    function exchangeRate( $amount, $currency, $exchangeIn )
    {
        $from = $currency;
        $to = $exchangeIn;
        $url = 'http://finance.yahoo.com/d/quotes.csv?f=l1d1t1&s='.$from.$to.'=X';


        try {
            $handle = fopen($url, 'r');

            if ($handle) {
                $result = fgetcsv($handle);
                fclose($handle);
                return $result[0] * $amount;
            }

        }catch (ContextErrorException $ex){}
        return $amount * 0.8;
    }




}