<?php

namespace App\Subscriber;

use App\Entity\Payment;
use App\Service\PaymentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class PaymentSubscriber implements EventSubscriber
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist => 'calculate',
            Events::postRemove => 'calculate',
            Events::postUpdate => 'calculate',
        ];
    }

    public function calculate(string $action, LifecycleEventArgs $args): void
    {
        $payment = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$payment instanceof Payment) {
            return;
        }

        $this->paymentService->calculateAcountBalance($payment);
    }
}
