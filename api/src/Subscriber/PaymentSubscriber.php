<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Acount;
use App\Entity\Payment;
use App\Service\PaymentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PaymentSubscriber implements EventSubscriberInterface
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['checkAcount', EventPriorities::POST_READ],
            KernelEvents::VIEW => ['calculateAcount', EventPriorities::POST_WRITE],
        ];
    }

    /*
     * This function hooks into the payment validition to make sure an payment acount is an acount
     *
     * It therby provides functionality for matching and creating acounts on the fly
     *
     * @parameter $event ViewEvent
     * @return Payment
     *
     */
    public function checkAcount(ViewEvent $event)
    {
        $payment = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$payment instanceof Payment || $method != 'POST'  ) { //|| !$payment->getAcount()
            return;
        }

        // Lets see if the acount is an acount
        if (!$payment->getAcount() instanceof Acount) {

            // OKe lets see if we can finde the acoutnt
            $acount = $this->em->getRepository('App:Acount')->findOneByResource($payment->getAcount());

            // If no acount can be found lets make one
            if(!$acount){
                $acount = New Acount();
                $acount->setResource($payment->getAcount());
            }

            $payment->setAcount($acount);
        }

        return $payment;
    }

    /*
     * This function calculates the new acount total afhter a payment has been added
     *
     * @parameter $event ViewEvent
     * @return Payment
     *
     */
    public function calculateAcount(ViewEvent $event)
    {

        $payment = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$payment instanceof Payment ) {
            return;
        }

        $this->paymentService->calculateAcountBalance($payment);

        return $payment;
    }
}
