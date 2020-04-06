<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class routeController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function indexPage(): Response
    {
        return $this->render('logs/login.html.twig');
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginPage(): Response
    {
        return $this->render('logs/login.html.twig');
    }

    /**
     * @Route("/join", name="join")
     * @return Response
     */
    public function joinPage(): Response
    {
        return $this->render('logs/join.html.twig');
    }

    /**
     * @Route("/password/new", name="passwordNew")
     * @return Response
     */
    public function passwordNewPage(): Response
    {
        return $this->render('logs/passwordnew.html.twig');
    }

}