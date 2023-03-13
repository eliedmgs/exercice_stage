<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Module;
use App\Entity\Apprenant;
use App\Entity\Video;
use App\Form\VideoType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class VideoController extends AbstractController
{
    //METTRE REQUIREMENTS
    /**
     * @Route("/formateur/module/ajouter_video/{id}", name="ajouter_video")
     */
    public function ajouterVideo(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);
        $video = new Video();          //creation objet nouvel  video
        $form = $this -> createForm(VideoType::class, $video); // creation du form par rapport a nouvel objet
        $form->handleRequest($request); // rempli la variable  formulaire avec ce qu ecrit l'utilisateur

        if($form->isSubmitted() && $form->isValid()) { //"si formulaire rempli et valide ...
            $file = $video->getFile(); //recuperation du fichier "affiche"
            $nbPdfs = count($module->getPdfs());
            $nbVideos = count($module->getVideos());
            $nbQcms = count($module->getQcms());
            
            if ($file != null) { //si fichier different de null
                $video->setExtension($file->guessExtension());// recuperation de l'extension de l'affiche
            }
            $entityManager= $doctrine->getManager();  // recuperation de l' entité manager
            $video->setModule($module);
            $video->setOrdre($nbPdfs + $nbVideos + $nbQcms + 1);
            $entityManager->persist($video);                  // " alors tu envois les info recuperees dans la bd
            $entityManager->flush();
            $this->addFlash('notice', 'la video a bien été ajoutée');  // message flash SUCCèS - "sac rempli" sera vidé dans twig listevideos
            if ($file != null) {    //si fichier different de null
                $chemin = $this->getParameter('dossier_video');//défini dans config/services.yaml
                $nomFichier = "file".$video->getId().".".$file->guessExtension(); // recuperation du nom du fichier concaténé avec son ID
                try {
                    $file ->move($chemin, $nomFichier);
                }catch (FileException $e){
                    $this->addFlash('erreur', 'Problème upload affiche : '.$e); // message flash ERREUR - "sac rempli" sera vidé dans twig listefilms
                }
            }
            return $this->redirectToRoute('module', ['id' => $id]); // une fois valide redirection vers liste des videos
        }
       
        return $this->render('video/ajouterVideo.html.twig', [
            'form'=>  $form->createView(),
        ]);
    }


    
/********************************************************    SUPPRIMER video      ************************************************ */

     /**
     * @Route("/formateur/module/video/supprimer/{id}", name="supprimer_video")
     */

    public function supprimerVideo($id, PersistenceManagerRegistry $doctrine): Response
    {

        $video = $doctrine->getRepository(Video::class)->find($id);
        $ordre = $video->getOrdre();
        
        $module = $video->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf) {
            if($pdf->getOrdre() > $ordre){
                $pdf->setOrdre($pdf->getOrdre() - 1);
                $entityManager->persist($pdf);
            }
        }
        foreach($module->getVideos() as $video) {
            if($video->getOrdre() > $ordre){
                $video->setOrdre($video->getOrdre() - 1);
                $entityManager->persist($video);
            }
        }
        foreach($module->getQcms() as $qcm) {
            if($qcm->getOrdre() > $ordre){
                $qcm->setOrdre($qcm->getOrdre() - 1);
                $entityManager->persist($qcm);
            }
        }
        $entityManager->remove($video);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Super la video a été supprimée avec succès!'
        
        );

        return $this->redirectToRoute('module', ['id'=>  $module->getId() ]);/*si valide vers cette route*/
     }


    



            

    /******************************************************     MODIFIER VIDEO      *************************************************** */

     /**
     * @Route("/formateur/module/video/modifier/{id}", name="modifier_video")
     */
    public function modifierVideo(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $video = $doctrine->getRepository(Video::class)->find($id);
        $module = $video->getModule();
        $form = $this->createForm( VideoType::class, $video);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager= $doctrine->getManager();
            $file = $video->getFile();

            if($file != null) {
                $video->setExtension($file->guessExtension());
                $chemin = $this->getParameter('dossier_video');
            }
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'La vidéo a été modifiée'
            );
            if($file != null) {
                $filesystem = new Filesystem();

                if($filesystem->exists('"file".$video->getId().".".$file->guessExtension()')) {

                    $filesystem->remove('"file".$video->getId().".".$file->guessExtension()');
            }
                try {
                    $file->move($chemin, "file".$video->getId().".".$file->guessExtension());
                }
                catch(FileException $e) {
                    $this->addFlash('uploadError', "l'upload de la vidéo à rencontrer un problème");
                }
            }

       
            return $this->redirectToRoute('module', ['id'=>  $module->getId() ]);
        }
        return $this->render('video/modifier_video.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/formateur/module/video/ordre_plus_video/{id}", name="ordre_plus_video")
     */

    public function ordrePlusVideo($id, PersistenceManagerRegistry $doctrine): Response
    {

        $video = $doctrine->getRepository(Video::class)->find($id);
        $ordre = $video->getOrdre();
        
        $module = $video->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf) {
            if($pdf->getOrdre() == ($ordre + 1)){
                $pdf->setOrdre($pdf->getOrdre() - 1);
                $video->setOrdre($ordre + 1);
                $entityManager->persist($pdf);
                $entityManager->persist($video);
            }
        }
        foreach($module->getVideos() as $video1) {
            if($video1->getOrdre() == ($ordre + 1)){
                $video1->setOrdre($video1->getOrdre() - 1);
                $video->setOrdre($ordre + 1);
                $entityManager->persist($video1);
                $entityManager->persist($video);
            }
        }
        foreach($module->getQcms() as $qcm) {
            if($qcm->getOrdre() == ($ordre + 1)){
                $qcm->setOrdre($qcm->getOrdre() - 1);
                $video->setOrdre($ordre + 1);
                $entityManager->persist($qcm);
                $entityManager->persist($video);
            }
        }
        
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "L'ordre a été modifié"
        
        );

        return $this->redirectToRoute('module', ['id'=>  $module->getId() ]);
    }

     /**
     * @Route("/formateur/module/video/ordre_moins_video/{id}", name="ordre_moins_video")
     */

    public function ordreMoinsVideo($id, PersistenceManagerRegistry $doctrine): Response
    {

        $video = $doctrine->getRepository(Video::class)->find($id);
        $ordre = $video->getOrdre();
        
        $module = $video->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf) {
            if($pdf->getOrdre() == ($ordre - 1)){
                $pdf->setOrdre($pdf->getOrdre() + 1);
                $video->setOrdre($ordre - 1);
                $entityManager->persist($pdf);
                $entityManager->persist($video);
            }
        }
        foreach($module->getVideos() as $video1) {
            if($video1->getOrdre() == ($ordre - 1)){
                $video1->setOrdre($video1->getOrdre() + 1);
                $video->setOrdre($ordre - 1);
                $entityManager->persist($video1);
                $entityManager->persist($video);
            }
        }
        foreach($module->getQcms() as $qcm) {
            if($qcm->getOrdre() == ($ordre - 1)){
                $qcm->setOrdre($qcm->getOrdre() + 1);
                $video->setOrdre($ordre - 1);
                $entityManager->persist($qcm);
                $entityManager->persist($video);
            }
        }
        
        $entityManager->flush();

        $this->addFlash(
            'notice',
            "L'ordre a été modifié"
        
        );

        return $this->redirectToRoute('module', ['id'=>  $module->getId() ]);
    }


    




    
}
