<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Acount;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment as Environment;

//use App\Service\MailService;
//use App\Service\MessageService;

class BookingSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, Environment $twig)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['template', EventPriorities::PRE_VALIDATE],
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
    public function payment(ViewEvent $event)
    {
        $payment = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$payment instanceof Payment || $method != 'POST') {
            return;
        }

        // Lets see if the acount is an acount
        if (!$payment->getAcount() instanceof Acount) {

            // OKe lets see if we can finde the acoutnt
            $acount = $this->em->getRepository('AppBundle:Acount')->findOneByResource($payment->getAcount());

            // If no acount can be found lets make one
            if(!$acount){
                $acount = New Acount();
                $acount->setResource($payment->getAcount());
            }

            $payment->setAcount($acount);
        }

        return $payment;
    }
}
