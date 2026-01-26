<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        //demande de traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            // Hash pass
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('motDePasse')->getData()
            );
            $user->setPassword($hashedPassword);

            $user->setRoles(['ROLE_USER']);

            // enregistrer le user
            $entityManager->persist($user);
            $entityManager->flush();

            // connexion auto
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            //redirection login
            //return $this->redirectToRoute('app_login');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
