<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Acount;
use App\Entity\Payment;
use App\Service\PaymentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CheckAcountSubscriber implements EventSubscriberInterface
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['checkAcount', EventPriorities::PRE_VALIDATE],
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

        if (!$payment instanceof Payment || $method != 'POST' || $payment->getAcount() || !$payment->getResource()) {
            return;
        }

        $payment = $this->paymentService->getAcount($payment);

        $event->setControllerResult($payment);

        return $event;
    }
}
