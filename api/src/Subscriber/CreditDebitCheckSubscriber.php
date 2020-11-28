<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Acount;
use App\Entity\Payment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreditDebitCheckSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['checkCredit', EventPriorities::PRE_WRITE],
        ];
    }

    /*
     * This function checks whether CREDIT and DEBIT have both been filled on an payment
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


        if (!$payment instanceof Payment || !$payment->getCredit() || !$payment->getDebit() ) {
            return;
        }

        //throw new InvalidValueException('A payment must either be CREDIT or DEBIT but can\'t be both');
        throw new BadRequestHttpException('A payment must either be CREDIT or DEBIT but can\'t be both');

        return $event;
    }

}
