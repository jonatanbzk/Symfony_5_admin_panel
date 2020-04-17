<?php


namespace App\Controller\Mailer;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Email extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Email constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $subject
     * @param $name
     * @param $email
     * @param $link
     */
    public function index($subject, $name, $email, $link)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom('myowndictionaryinfo@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    ['name' => $name, 'email_link' => $link]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        return;
    }

}