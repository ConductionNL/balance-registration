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

class CalculateBalanceSubscriber implements EventSubscriberInterface
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['calculateAcount', EventPriorities::POST_WRITE],
        ];
    }

    /*
     * This function calculates the new acount total afther a payment has been added
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

        return $event;
    }
}
