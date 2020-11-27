<?php

namespace App\Service;

use App\Entity\Acount;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    private $em;

    public function __construct( EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /*
     * This updates the balance of an acount that us atached to a payment and is used to recalcualte the balance afther a payment has been made
     *
     * @parameter $payment Payment
     * @return Payment
     */
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

    /*
     * This function provides an acount for payments if none is given
     *
     * @parameter $payment Payment
     * @return Payment
     */
    public function getAcount(Payment $payment)
    {

        // Lets see if the acount is an acount
        if (!$payment->getAcount()) {

            // OKe lets see if we can finde the acount
            $acount = $this->em->getRepository('App:Acount')->findOneByResource($payment->getResource());

            // If no acount can be found lets make one
            if(!$acount){
                $acount = New Acount();
                $acount->setResource($payment->getResource());
                $acount->setReference('primary');
            }

            $payment->setAcount($acount);
        }

        return $payment;
    }
}
