<?php

namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    public function calculateAcountBalance(Payment $payment, EntityManagerInterface $em)
    {
        $acount = $payment->getAcount();
        $balance = $em->getRepository('AppBundle:Payment')
            ->calculateAcountBalance($acount);
        $acount->setBelance($balance);

        $em->persist($acount);
        $em->flush();

        return $payment;
    }

}
