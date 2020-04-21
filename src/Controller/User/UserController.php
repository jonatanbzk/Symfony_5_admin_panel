<?php

namespace App\Controller\User;

use App\Controller\Mailer;
use App\Entity\User;
use App\Form\User\RegistrationFormType;
use App\Form\User\UpdateUserFormType;
use App\Form\User\UpdateUserPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Mailer\Email $sendEmail
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface
    $passwordEncoder, Mailer\Email $sendEmail): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setActivationCode(md5(rand()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $subject = 'Email verification';
            $view = 'emails/registration.html.twig';
            $name = $user->getUsername();
            $email = $user->getEmail();
            $link = 'http://127.0.0.1:8000/activCode/' . $user->getId() .
                '/' . $user->getActivationCode();
            $sendEmail->index($subject, $name, $email, $view, $link);
            $this->addFlash('success', 'You have been 
            registered, please check your email');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset_password_form/{id}", name="reset_password_form")
     * @param Request $request
     * @return Response
     */
    public function resetPasswordForm(Request $request,
                                      UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(UpdateUserPasswordFormType::class);
        $form->handleRequest($request);

        $routeParameters = $request->attributes->get('_route_params');
        $id = $routeParameters['id'];

        $repository = $this->getDoctrine()->getRepository(
            User::class);
        $user = $repository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('plainPassword')->getData())) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'You updated your 
            password successfully.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('resetpassword/passwordnew_form.html.twig', [
            'user' => $user,
            'updateUserForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/settings/{id}", name="user_update", methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(User $user, Request $request,
                           UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UpdateUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('plainPassword')->getData())) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'You updated your account
             successfully.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('settingspage/settingspage.html.twig', [
            'user' => $user,
            'updateUserForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/activCode/{id}/{code}", name="activCode")
     * @param Request $request
     * @return Response
     */
    public function emailValidation(Request $request): Response
    {
        $routeParameters = $request->attributes->get('_route_params');
        $id = $routeParameters['id'];
        $code = $routeParameters['code'];

        $repository = $this->getDoctrine()->getRepository(
            User::class);
        $user = $repository->find($id);

        if ($user != null and $user->getId() == $id and
            $user->getActivationCode() == $code) {
            $user->setEmailValid(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Your email is now validated');

        } else {
            $this->addFlash('danger', 'You don\'t have any account');;
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/settings/delete/{id}", name="user_delete", methods="DELETE")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(User $user, Request $request)
    {
        if ($this->isCsrfTokenValid
        ('delete' . $user->getId(), $request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            // necessary to redirect
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
        }
        return $this->redirectToRoute('app_logout');
    }

}
