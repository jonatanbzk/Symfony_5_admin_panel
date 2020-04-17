<?php


namespace App\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ActivationCode extends AbstractController
{

    /**
     * @Route(/activCode/{code}, name="active_code")
     */
    public function activateCode()
    {

    }

}