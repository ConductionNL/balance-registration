<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Acount;
use App\Entity\Payment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class CreditLimitCheckSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['checkCredit', EventPriorities::PRE_WRITE],
        ];
    }

    /*
     * This function checks whether or not an acount creditLimit would be passed by acepting this payment
     *
     *
     * @parameter $event ViewEvent
     * @return Payment
     *
     */
    public function checkCredit(ViewEvent $event)
    {
        $payment = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$payment instanceof Payment || $method != 'POST' || !$payment->getDebit() || !$payment->getAcount()->getCreditLimit()) {
            return;
        }

        // Lets see if we pass the credit limit
        $newBalance = $payment->getAcount()->getBalance() - $payment->getDebit();
        if (abs($newBalance) > $payment->getAcount()->getCreditLimit()) {
            throw new BadRequestHttpException('This acount has insufficient credit limit to handle this request');
        }

        return $event;
    }
}
