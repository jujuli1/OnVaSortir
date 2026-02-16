<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {

        $ville =  new Ville();
        $ville->setNom('VilleTest');
        $ville->setCp('12345');
        $manager->persist($ville);

        $campus = new Campus();
        $campus->setNom('Campus principal');
        $campus->setAdresse('AdresseDeTest');
        $campus->setVille($ville);
        $manager->persist($campus);


        //user de test
        $user = new Utilisateur();
        $user->setNom('Test');
        $user->setPrenom('Playwright');
        $user->setEmail('test@playwright.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setBirthday(new \DateTime('now'));
        $user->setCampus($campus);



        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'Jujujujuvq2pq&'
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
