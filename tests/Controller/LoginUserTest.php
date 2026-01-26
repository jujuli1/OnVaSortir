<?php

namespace App\Tests\Controller;
use App\Entity\Campus;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginUserTest extends WebTestCase
{


    private $entityManager;






    public function testIndex(): void
    {


        //simule navigateur
        $client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();


        // créer un user de test
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => 'test@test.com']);

        if(!$user){

            $ville = new Ville();
            $ville->setNom('test Campus Ville');
            $ville->setCP('91420');

            $this->entityManager->persist($ville);

            $campus = new Campus();
            $campus->setNom('test Campus');
            $campus->setAdresse('test Campus Adresse');
            $campus->setVille($ville);
            $user = new Utilisateur();
            $user->setEmail('test@test.com');
            $user->setPassword('test', PASSWORD_BCRYPT);
            $user->setRoles(['ROLE_USER']);
            $user->setNom('Kul');
            $user->setPrenom('Testy');
            $user->setBirthday(new \DateTime());
            $this->entityManager->persist($campus);
            $user->setCampus($campus);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        $crawler = $client->request('GET', '/login');

        //remplit le formulaire
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@test.com',
            'password' => 'test',
        ]);

        $client->submit($form);

        //redirection
        $client->followRedirect();

        //verif connexion
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert');



    }

    //test login raté
    public function testLoginFail(): void{

        //simule navigateur
        $client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $crawler = $client->request('GET', '/login');

        //recup champ token csrf
        $csrfToken = $crawler->filter('input[name="_csrf_token"]')->attr('value');

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@test.com',
            'password' => 'oupsy',
            '_csrf_token' => $csrfToken,
        ]);

        $client->submit($form);

        // pas de redirection
        echo $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');

}

    //supprime user
    protected function tearDown(): void{



        $entityManager = static::getContainer()->get('doctrine')->getManager();


        $user = $entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['email' => 'test@test.com']);
        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
        }
        parent::tearDown();

    }




}
