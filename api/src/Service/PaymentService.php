<?php

namespace App\Service;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    private $em;

    public function __construct( EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function calculateAcountBalance(Payment $payment)
    {

        $acount = $payment->getAcount();
        $balance =  $this->em->getRepository('App:Payment')
            ->calculateAcountBalance($acount);

        $acount->setBalance($balance);

        $this->em->persist($acount);
        $this->em->flush();

        return $payment;
    }

}
