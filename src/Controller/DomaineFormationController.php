<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Formation;
use App\Entity\DomaineFormation;
use App\Form\DomaineFormationType;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class DomaineFormationController extends AbstractController
{
    /**
     * @Route("/admin/liste_domaines_formations", name="admin_liste_domaines_formations")
     */
    public function listeDomainesFormations(PersistenceManagerRegistry $doctrine): Response
    {
        $domainesFormations = $doctrine->getRepository(DomaineFormation::class)->findAll();
        

        return $this->render('admin/listeDomainesFormations.html.twig', [
            'domainesFormations' => $domainesFormations,
        ]);
    }

    /**
     * @Route("/admin/ajouter_domaine_formation", name="admin_ajouter_domaine_formation") 
     */
    
    public function ajouterDomaineFormation(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $domaineFormation = new DomaineFormation(); 
        $form = $this->createForm(DomaineFormationType::class, $domaineFormation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $imageFile= $form->get('image')->getData();

            if($imageFile != null){
                $domaineFormation->setExtension($imageFile->guessExtension());
            };
            

            $entityManager= $doctrine->getManager();
            $entityManager->persist($domaineFormation);
            $entityManager->flush(); 


            if($imageFile != null){
                try {
                    $imageFile->move(
                        $this->getParameter('dossier_domaine_formation'),
                        "imgDomaineFormation".$domaineFormation->getId().'.'.$imageFile->guessExtension()
                    );

                } catch (FileException $e) {
                    $e->error("Le fichier n'est pas valide");
                }
            }

                    $this->addFlash( 
                        'notice', 
                        'Super le domaine de formation a été ajouté avec succès!'
                    );
    
            return $this->redirectToRoute('admin_liste_domaines_formations');
        }
 
        return $this->render('admin/ajouterDomaineFormation.html.twig', [ 
            'form'=> $form->createView(),

    ]);
    }

    /**
     * @Route("/admin/modifier_domaine_formation/{id}", name="admin_modifier_domaine_formation")
     */
    public function modifierDomaineFormation(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $domaineFormation = $doctrine->getRepository(DomaineFormation::class)->find($id);
        $form = $this->createForm(DomaineFormationType::class, $domaineFormation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $image= $form->get('image')->getData();

            if($image != null){
                $ancienneExtension = $domaineFormation->getExtension();
                $domaineFormation->setExtension($image->guessExtension());
            };

            $entityManager= $doctrine->getManager();
            $entityManager->persist($domaineFormation);
            $entityManager->flush();

            if($image != null){
                $filesystem = new Filesystem();
                //ON VERIFIE SI UN POSTER A DEJA ETE UPLOADER
                if ($filesystem->exists("imgDomaineFormation".$domaineFormation->getId().'.'.$ancienneExtension)){
                    //Si L'AFFICHE EXISTE ON LA SUPPRIME
                    $filesystem->remove("imgDomaineFormation".$domaineFormation->getId().'.'.$ancienneExtension);
                }
                try {
                    $image->move(
                        $this->getParameter('dossier_domaine_formation'),
                        "imgDomaineFormation".$domaineFormation->getId().'.'.$image->guessExtension()
                    );

                } catch (FileException $e) {
                    $e->error("Le fichier n'est pas valide");
                }
            }

            $this->addFlash(
                'notice',
                'Le domaine de formation a été modifié'
            );

         
       
            return $this->redirectToRoute('admin_liste_domaines_formations');
        }
        return $this->render('admin/modifier_domaine_formation.html.twig', [
        'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/domaine_formation/supprimer/{id}", name="admin_supprimer_domaine_formation")
     */

    public function supprimerDomaineFormation($id, PersistenceManagerRegistry $doctrine): Response
    {

        $domaineFormation = $doctrine->getRepository(DomaineFormation::class)->find($id);
        $image= $domaineFormation->getImage();
        if($image != null){
            $filesystem = new Filesystem();
            //ON VERIFIE SI UN POSTER A DEJA ETE UPLOADER
            if ($filesystem->exists($this->getParameter('dossier_domaine_formation'), "imgDomaineFormation".$domaineFormation->getId().'.'.$domaineFormation->getExtension())){
                //Si L'AFFICHE EXISTE ON LA SUPPRIME
                $filesystem->remove($this->getParameter('dossier_domaine_formation')."/imgDomaineFormation".$domaineFormation->getId().'.'.$domaineFormation->getExtension());
            }
        }

        $entityManager= $doctrine->getManager();
        $entityManager->remove($domaineFormation);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'le domaine de formation a été supprimé avec succès!'
        
        );

        return $this->redirectToRoute('admin_liste_domaines_formations');/*si valide vers cette route*/
     }
}
