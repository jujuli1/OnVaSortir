<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Process\Process;


final class ProfilController extends AbstractController
{
    //Route profil
    #[Route('/profil', name: 'app_profil')]
    public function publicIndex(EntityManagerInterface $em,Request $request,
                                SluggerInterface $slugger,): Response
    {
        return $this->index($em,$request,$slugger);
    }

    protected function index(EntityManagerInterface $em,Request $request,
                          SluggerInterface $slugger): Response
    {


        $user = $this->getUser();
        $inscriptions = $em->getRepository(Inscription::class)->findBy(['utilisateur' => $user]);

        //formulaire photo de profil
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);


        //formulaire photo de profil
        if ($form->isSubmitted()) {

            $photoFile = $form['photo']->getData();


            if ($photoFile) {
                //retire extention fichier
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                //genere id unique
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                //deplacement photo
                $photoFile->move(
                // a trouver dans service.yml
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                //chemin complet pour python
                $uploadedImagePath = $this->getParameter('photos_directory') . '/' . $newFilename;

                $referenceImagePath = $this->getParameter('photos_directory');

                $pythonScript = $this->getParameter('kernel.project_dir') . '/scripts/compareImg.py';

                $process = new Process([
                    'python',
                    $pythonScript,
                    $uploadedImagePath,
                    $referenceImagePath
                ]);



                $process->run();

                if (!$process->isSuccessful()) {
                    throw new \RuntimeException($process->getErrorOutput());
                }

                //recup toute les chaine de caractere du fichier .py
                $output = $process->getOutput();

                // Affiche ou log le score de comparaison
                dump($output);

                $user->setPhoto($newFilename);


            }
            $em->flush();


        }

        // resultat comparaison avec le derniere photo
        $resultatComparaison = [
            'image' => null,
            'score' => null,
        ];
        $lastUpload = $user->getPhoto();
        if ($lastUpload) {
            $resultatComparaison = $this->compareImg(
                $this->getParameter('photos_directory') . '/' . $lastUpload
            );
        }


        $sortie = new Sortie();
        $sortie->setOrganisateur($user->getNom());

        //formulaire de création d'une sortie
        $formCreateSortie = $this->createForm(SortieType::class, $sortie);
        $formCreateSortie->handleRequest($request);



        if ($formCreateSortie->isSubmitted() && $formCreateSortie->isValid()) {

            $photoFile = $formCreateSortie->get('photo')->getData();


            if ($photoFile) {
                //retire extention fichier
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                //genere id unique
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                $photoFile->move(
                // a trouver dans service.yml
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                $sortie->setPhoto($newFilename);


            }
            //id utilisateur connecté remplit automatiquement le champ utilisateur_id de la table sortie
            $sortie->setUtilisateur($this->getUser());
            $em->persist($sortie);
            $em->flush();






        }
        return $this->render('profil/index.html.twig', [
            'inscriptions' => $inscriptions,
            'user' => $user,
            'form' => $form->createView(),
            'formCreateSortie' => $formCreateSortie->createView(),
            'bestImage' => $resultatComparaison['image'],
            'score' => $resultatComparaison['score'],
        ]);
    }


        //fonction de comparaison d'image avec compareImg.p
        public function compareImg(string $imageUploadPath): array
        {
        $scriptPath = $this->getParameter('kernel.project_dir') . '/scripts/compareImg.py';
        $dossierRef = $this->getParameter('kernel.project_dir') . '/public/uploads/photos';



        $process = new Process(['python', $scriptPath, $imageUploadPath, $dossierRef]);

        $process->run();
        dump($process->getOutput());

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

            $output = explode("\n", $process->getOutput());

        //parser resultat
        $bestImage = null;
        $mseScore = null;


            foreach ($output as $line) {
                $line = trim($line); // enlève \r\n ou espaces

                if (stripos($line, "BEST_IMAGE:") === 0) {
                    $bestImage = trim(substr($line, strlen("BEST_IMAGE:")));
                }
                if (stripos($line, "MSE_SCORE:") === 0) {
                    $mseScore = (float)trim(substr($line, strlen("MSE_SCORE:")));
                }
            }

        return [
            'image'=> $bestImage,
            'score'=> $mseScore
        ];


    }


    #[Route('/profil/desistement/{id}', name: 'app_sortie_desistement')]
    public function desistement(int $id,EntityManagerInterface $em,): Response
    {

        // se desisté d'une sortie

        $user = $this->getUser();
        $sortie = $em->getRepository(Sortie::class)->find($id);

        // Recup linscription existante
        $inscription = $em->getRepository(Inscription::class)
            ->findOneBy([
                'utilisateur' => $user,
                'sortie' => $sortie
            ]);




        if($inscription){
            $em->remove($inscription);
            $em->flush();
            return $this->redirectToRoute('app_profil');
        }



        // Recup toutes les inscriptions de l utilisateur pour le profil
        $inscriptions = $em->getRepository(Inscription::class)
            ->findBy(['utilisateur' => $user]);



        return $this->render('profil/indexAdminRegister.html.twig',[
            'inscriptions' => $inscriptions,


        ]);
    }




}
