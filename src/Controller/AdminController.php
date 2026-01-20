<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\RegisterAdminType;
use App\Form\RegistrationFormType;
use App\Form\RegistrationUserAdminType;
use App\Form\UtilisateurType;
use App\Repository\CampusRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin')]
final class AdminController extends AbstractController
{

    #[Route('/profil', name: 'app_admin_profil')]
    public function adminProfil(EntityManagerInterface $em): Response
    {
        //affiche les sortie et users en bdd sur la page /vitrine
        $sorties = $em->getRepository(Sortie::class)->findAll();
        $utilisateur = $em->getRepository(Utilisateur::class)->findAll();

        return $this->render('admin/admin_profil.html.twig',[
            'sorties' => $sorties,
            'utilisateur' => $utilisateur,
        ]);
    }

    // route vers le profil utilisateur depuis le compte admin
    #[Route('/profil/user/{id}', name: 'app_profil_user')]
    public function profilUser( Utilisateur $user): Response
    {





        return $this->render('profil/profil_user.html.twig', [
            'utilisateurs' => $user,

        ]);
    }

    #[Route('/registerAdmin', name: 'app_admin_register')]
    public function index(Request $request,
                          UserPasswordHasherInterface $passwordHasher,
                          EntityManagerInterface $entityManager,
                          CampusRepository $campusRepository): Response
    {

        $user = new Utilisateur();
        $registrationAdminForm = $this->createForm(RegisterAdminType::class, $user);
        $registrationAdminForm->handleRequest($request);  // //!\\

        // campus par défault
        $campus = $campusRepository->find(1);
        $user->setCampus($campus);

        if($registrationAdminForm->isSubmitted() && $registrationAdminForm->isValid()) {

            // Hash pass
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $registrationAdminForm->get('motDePasse')->getData()
            );
            $user->setPassword($hashedPassword);

            // donne le rôle admin
            $user->setRoles(['ROLE_ADMIN']);

            // enregistrer le user
            $entityManager->persist($user);
            $entityManager->flush();

        }

        return $this->render('admin/indexAdminRegister.html.twig', [
            'registrationAdminForm' => $registrationAdminForm->createView(),
        ]);
    }

    #[Route('/registerUser', name: 'app_register_user_admin')]
    public function registerUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationUserAdminType::class, $user);
        $form->handleRequest($request);

        $user->setRoles(['ROLE_USER']);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère UNIQUEMENT ce que le formulaire a envoyé
            $rolesForm = $form->get('roles')->getData(); // ['ROLE_ADMIN'] ou []

            // Ajoute ROLE_USER
            $roles = $rolesForm;
            $roles[] = 'ROLE_USER';

            $user->setRoles(array_unique($roles));
            // Hash pass
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('motDePasse')->getData()
            );
            $user->setPassword($hashedPassword);



            // enregistrer le user
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('app_admin_profil');



        }

        return $this->render('admin/register_admin_user.html.twig', [
            'registrationForm' => $form->createView(),
        ]);




} }
