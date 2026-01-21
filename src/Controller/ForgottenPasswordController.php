<?php

namespace App\Controller;

use App\Form\ForgottenPasswordType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\Validator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ForgottenPasswordController extends AbstractController
{
    #[Route('/forgotten/password', name: 'app_forgotten_password')]
    public function index(Request $request,MailerInterface $mailer, UtilisateurRepository $userRepository, EntityManagerInterface $em): Response
    {

        //formulaire mdp oublié
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = $form->get('email')->getData();

            $user = $userRepository->findOneBy(['email' => $email]);

            $this->addFlash(
                'info',
                'Si un compte existe, un email a été envoyé.'
            );

            if ($user) {

                //générer token
                $token = bin2hex(random_bytes(64));

                $user->setResetToken($token);
                //expiration du token
                $user->setResetTokenExpiresAt(new \DateTime('+30 minutes'));



                //flush entité modifié vers bdd
                $em->flush();

                // envoit du mail
                $emailMessage = new TemplatedEmail()
                    ->from('no-reply@monapp.test')
                    ->to($user->getEmail())
                    ->subject('reinitialisation de votre mot de passe')
                    ->htmlTemplate('forgotten_password/reset_password.html.twig')
                    ->context([
                        'token' => $token,
                        'user' => $user,
                    ]);

                $mailer->send($emailMessage);

            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('forgotten_password/index.html.twig', [
            'form' => $form->createView(),

        ]);
    }
}
