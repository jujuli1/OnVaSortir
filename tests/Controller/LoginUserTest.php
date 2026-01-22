<?php

namespace App\Tests\Controller;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginUserTest extends WebTestCase
{


    private $entityManager;





    public function testIndex(): void
    {



        //simule navigateur
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // créer un user de test
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => 'test@test.com']);

        if(!$user){
            $user = new Utilisateur();
            $user->setEmail('test@test.com');
            $user->setPassword('test', PASSWORD_BCRYPT);
            $user->setRoles(['ROLE_USER']);
            $user->setNom('Kul');
            $user->setPrenom('Testy');
            $entityManager->persist($user);
            $entityManager->flush();
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
        $this->assertSelectorExists('.alert-deconnexion');


    }

    //test login raté
    public function testLoginFail(): void{

        //simule navigateur
        $client = static::createClient();

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
    $this->assertSelectorExists("alert");
}

    //supprime user
    protected function tearDown(): void{


        parent::tearDown();

        $user = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['email' => 'test@test.com']);
        if($user){
            $this->$user->remove($user);
            $this->$user->flush();
        }

        $this->$user->close();
        $this->$user = null;
    }


}
