<?php


namespace App\Controller\Pages;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsPageController extends AbstractController
{

    /**
     * @Route("/settings", name="settings")
     * @return Response
     */
    public function settingsPage(): Response
    {
        return $this->render('settingspage/settingspage.html.twig');
    }

}