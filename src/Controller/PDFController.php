<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Module;
use App\Entity\Apprenant;
use App\Entity\PDF;
use App\Form\PDFType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;



class PDFController extends AbstractController
{
  //METTRE REQUIREMENTS
    /**
     * @Route("/formateur/module/ajout_pdf/{id}", name="ajout_pdf")
     */
    public function ajouterPDF(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);
        $pdf = new PDF();          //creation objet nouvel  film
        $form = $this -> createForm(PDFType::class, $pdf); // creation du form par rapport a nouvel objet
        $form->handleRequest($request); // rempli la variable  formulaire avec ce qu ecrit l'utilisateur

        if($form->isSubmitted() && $form->isValid()) { //"si formulaire rempli et valide ...
            $file = $pdf->getFile(); //recuperation du fichier "affiche"
            $nbPdfs = count($module->getPdfs());
            $nbVideos = count($module->getVideos());
            $nbQcms = count($module->getQcms());
            if ($file != null) { //si fichier different de null
                $pdf->setExtension($file->guessExtension());// recuperation de l'extension de l'file
            }
            $entityManager= $doctrine->getManager();  // recuperation de l' entité manager
            $pdf->setModule($module);
            $pdf->setOrdre($nbPdfs + $nbVideos + $nbQcms + 1);
            $entityManager->persist($pdf);                  // " alors tu envois les info recuperees dans la bd
            $entityManager->flush();
            $this->addFlash('notice', 'Le PDF a bien été ajoutée');  // message flash SUCCèS - "sac rempli" sera vidé dans twig listepdfs
            if ($file != null) {    //si fichier different de null
                $chemin = $this->getParameter('dossier_pdf');//défini dans config/services.yaml
                $nomFichier = "file".$pdf->getId().".".$file->guessExtension(); // recuperation du nom du fichier concaténé avec son ID
                try {
                    $file ->move($chemin, $nomFichier);
                }catch (FileException $e){
                    $this->addFlash('erreur', 'Problème upload affiche : '.$e); // message flash ERREUR - "sac rempli" sera vidé dans twig listefilms
                }
            }
            return $this->redirectToRoute('module', ['id' => $id]); // une fois valide redirection vers liste des films
        }
       
        return $this->render('pdf/ajout_pdf.html.twig', [
            'form'=>  $form->createView(),
        ]);
    }

/********************************************************    SUPPRIMER PDF      ************************************************ */

     /**
     * @Route("/formateur/module/pdf/supprimer/{id}", name="supprimer_pdf")
     */

    public function supprimerPdf($id, PersistenceManagerRegistry $doctrine): Response
    {

        $pdf = $doctrine->getRepository(PDF::class)->find($id);
        $ordre = $pdf->getOrdre();
        $module = $pdf->getModule();

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
        $entityManager->remove($pdf);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Super le pdf a été supprimé avec succès!'
        
        );

        return $this->redirectToRoute('module', ['id'=>  $module->getId() ]);/*si valide vers cette route*/
     }


/******************************************************     MODIFIER PDF      *************************************************** */

     /**
     * @Route("/formateur/module/pdf/modifier/{id}", name="modifier_pdf")
     */
    public function modifierPdf(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $pdf = $doctrine->getRepository(PDF::class)->find($id);
          
        $form = $this->createForm( PDFType::class, $pdf);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {

            $entityManager= $doctrine->getManager();
            $file = $pdf->getFile();

            if($file != null) {
                $pdf->setExtension($file->guessExtension());
                $chemin = $this->getParameter('dossier_pdf');
            }
            $entityManager->persist($pdf);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Le PDF a été modifié'
            );
            if($file != null) {
                $filesystem = new Filesystem();

                if($filesystem->exists('"file".$pdf->getId().".".$file->guessExtension()')) {

                    $filesystem->remove('"file".$pdf->getId().".".$file->guessExtension()');
            }
                try {
                    $file->move($chemin, "file".$pdf->getId().".".$file->guessExtension());
                }
                catch(FileException $e) {
                    $this->addFlash('uploadError', "l'upload du pdf à rencontrer un problème");
                }
            }

       
            return $this->redirectToRoute('module', ['id'=> $pdf->getModule()->getId() ]);
        }
        return $this->render('pdf/modifier_pdf.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/formateur/module/pdf/ordre_plus_pdf/{id}", name="ordre_plus_pdf")
     */

    public function ordrePlusPdf($id, PersistenceManagerRegistry $doctrine): Response
    {

        $pdf = $doctrine->getRepository(PDF::class)->find($id);
        $ordre = $pdf->getOrdre();
        
        $module = $pdf->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf1) {
            if($pdf1->getOrdre() == ($ordre + 1)){
                $pdf1->setOrdre($pdf1->getOrdre() - 1);
                $pdf->setOrdre($ordre + 1);
                $entityManager->persist($pdf1);
                $entityManager->persist($pdf);
            }
        }
        foreach($module->getVideos() as $video) {
            if($video->getOrdre() == ($ordre + 1)){
                $video->setOrdre($video->getOrdre() - 1);
                $pdf->setOrdre($ordre + 1);
                $entityManager->persist($video);
                $entityManager->persist($pdf);
            }
        }
        foreach($module->getQcms() as $qcm) {
            if($qcm->getOrdre() == ($ordre + 1)){
                $qcm->setOrdre($qcm->getOrdre() - 1);
                $pdf->setOrdre($ordre + 1);
                $entityManager->persist($qcm);
                $entityManager->persist($pdf);
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
     * @Route("/formateur/module/pdf/ordre_moins_pdf/{id}", name="ordre_moins_pdf")
     */

    public function ordreMoinsPdf($id, PersistenceManagerRegistry $doctrine): Response
    {

        $pdf = $doctrine->getRepository(PDF::class)->find($id);
        $ordre = $pdf->getOrdre();
        
        $module = $pdf->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf1) {
            if($pdf1->getOrdre() == ($ordre - 1)){
                $pdf1->setOrdre($pdf1->getOrdre() + 1);
                $pdf->setOrdre($ordre - 1);
                $entityManager->persist($pdf1);
                $entityManager->persist($pdf);
            }
        }
        foreach($module->getVideos() as $video) {
            if($video->getOrdre() == ($ordre - 1)){
                $video->setOrdre($video->getOrdre() + 1);
                $pdf->setOrdre($ordre - 1);
                $entityManager->persist($video);
                $entityManager->persist($pdf);
            }
        }
        foreach($module->getQcms() as $qcm) {
            if($qcm->getOrdre() == ($ordre - 1)){
                $qcm->setOrdre($qcm->getOrdre() + 1);
                $pdf->setOrdre($ordre - 1);
                $entityManager->persist($qcm);
                $entityManager->persist($pdf);
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
