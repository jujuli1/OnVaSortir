<?php
// tests/Entity/UtilisateurCascadeTest.php

namespace App\Tests\Entity;

use App\Entity\Campus;
use App\Entity\Inscription;
use App\Entity\Utilisateur;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtilisateurCascadeTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    public function testOnDeleteCascade(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        // création campus pour utilisateur
        $campus = new Campus();
        $campus->setNom('Campus Test');
        $campus->setAdresse('1 Rue Test');
        $ville = new Ville();
        $ville->setNom('Ville Test');
        $ville->setCp('12345');
        $entityManager->persist($ville);
        $campus->setVille($ville);
        $entityManager->persist($campus);

        // creation utilisateur
        $user = new Utilisateur();
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setEmail('jean.dupont@test.com');
        $user->setMotDePasse('Password1');
        $user->setBirthday(new \DateTime('2000-01-01'));

        $campus = $entityManager->getRepository(Campus::class)->find(1);
        $user->setCampus($campus);

        $entityManager->persist($user);

        // créer une sortie
        $sortie = new Sortie();
        $sortie->setNom('Sortie cascade test');
        $sortie->setDateHeure(new \DateTime('+1 day'));
        $sortie->setOrganisateur('Organisateur Test');
        $sortie->setStatus(true);
        $sortie->setUtilisateur($user);
        $entityManager->persist($sortie);

        // creation inscription
        $inscription = new Inscription();
        $inscription->setUtilisateur($user);
        $inscription->setSortie($sortie);
        $inscription->setDateInscription(new \DateTime());
        $inscription->setStatusInscription(true);
        $entityManager->persist($inscription);

        $user->addInscription($inscription);
        $sortie->addInscription($inscription);


        $entityManager->flush();

        // test on delete cascade
        $userId = $user->getId();
        $entityManager->remove($user);
        $entityManager->flush();

       /* $sortieId = $sortie->getId();
        $deletedSortie = $entityManager->getRepository(Sortie::class)->find($sortieId);
        $this->assertNull($deletedSortie, "La sortie n'a pas été supprimée en cascade !");*/

        //supp inscription pour test
        $inscriptions = $entityManager->getRepository(Inscription::class)->findBy(['utilisateur' => $userId]);
        $this->assertCount(0, $inscriptions);
    }



    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
