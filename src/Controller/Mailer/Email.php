<?php


namespace App\Controller\Mailer;


use App\Entity\User;
use App\Form\User\ResetPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class Email extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    private $session;

    /**
     * Email constructor.
     * @param \Swift_Mailer $mailer
     * @param SessionInterface $session
     */
    public function __construct(\Swift_Mailer $mailer, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->session = $session;
    }

    /**
     * @param $subject
     * @param $name
     * @param $email
     * @param $view
     * @param $link
     */
    public function index($subject, $name, $email, $view, $link)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom('senderEmail@domain.com') //TODO : change email
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    $view,
                    ['name' => $name, 'email_link' => $link]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        return;
    }

    /**
     * @Route("/resend_email", name="resend_email")
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse
     */
    public function reSendUserEmail(AuthenticationUtils $authenticationUtils): Response
    {
        $username = $authenticationUtils->getLastUsername();
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('username' => $username));
        $subject = 'Email verification';
        $view = 'emails/registration.html.twig';
        $name = $user->getUsername();
        $email = $user->getEmail();
        $link = 'http://127.0.0.1:8000/activCode/' . $user->getId() .
            '/' . $user->getActivationCode();
        $this->index($subject, $name, $email, $view, $link);
        $this->addFlash('success', 'An new verification email 
        has been send');
        $this->session->set('errorEmail', '');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/reset_password_mail", name="reset_password_mail")
     * @param Request $request
     * @return Response
     */
    public function resetPassword(Request $request)
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(array('email' => $email));
            if ($user != null) {
                $subject = 'Reset password';
                $view = 'emails/resetPassword.html.twig';
                $name = $user->getUsername();
                $email = $user->getEmail();
                $link = 'http://127.0.0.1:8000/reset_password_form/' .
                    $user->getId();
                $this->index($subject, $name, $email, $view ,$link);
                $this->addFlash('success', 'You will receive an 
                email with instructions about how to reset your password');
            } else {
                $this->addFlash('danger', 'You don\'t have any 
            account with this email adress');
            }
        }
        return $this->render('resetpassword/passwordnew.html.twig', [
            'resetpasswordForm' => $form->createView(),
        ]);
    }

}