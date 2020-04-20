<?php


namespace App\Controller\Logs;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerification extends AbstractController
{

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

}
