<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\QCM;
use App\Entity\Module;
use App\Form\QCMType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;



class QcmController extends AbstractController
{
    /**
     * @Route("/formateur/module/qcm/{id}", name="qcm")
     */
    public function qcm($id, PersistenceManagerRegistry $doctrine): Response
    {
        $qcm = $doctrine->getRepository(QCM::class)->find($id);


   
    
        return $this->render('qcm/qcm.html.twig', [
            'qcm'=>  $qcm,
            ]);
    }

    /**
     * @Route("/formateur/module/qcm/ajout/{id}", name="ajout_qcm")
     */
    public function ajoutQcm($id, Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);
        $qcm = new QCM();
        $form = $this -> createForm( QCMType::class, $qcm); // creation du form par rapport a nouvel objet
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $nbPdfs = count($module->getPdfs());
            $nbVideos = count($module->getVideos());
            $nbQcms = count($module->getQcms());
            $entityManager= $doctrine->getManager();
            $qcm->setModule($module);
            $qcm->setOrdre($nbPdfs + $nbVideos + $nbQcms + 1);
            $entityManager->persist($qcm);
            $entityManager->flush();
            $this->addFlash('notice', 'Le QCM bien été ajouté');

            return $this->redirectToRoute('module', ['id' => $id]);
        }

        return $this->render('qcm/ajout_qcm.html.twig', [

            'form'=> $form->createView(),

        ]);

        
    }

    /*****************************************************   QCM : SUPPRIMER  *************************************************/

     /**
     * @Route("/formateur/module/qcm/supprimer/{id}", name="supprimer_qcm")
     */

    public function supprimerQcm($id, PersistenceManagerRegistry $doctrine): Response
    {

        $qcm = $doctrine->getRepository(QCM::class)->find($id);
        $ordre = $qcm->getOrdre();
        $module = $qcm->getModule();
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
        $entityManager->remove($qcm);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Super le QCM a été supprimé avec succès!'
        
        );

        return $this->redirectToRoute('module', ['id'=> $module->getId() ]);/*si valide vers cette route*/
     }


     /*************************************************     QCM : MODIFIER  ***************************************************/

     /**
     * @Route("/formateur/module/qcm/modifier/{id}", name="modifier_qcm")
     */
    public function modifierQcm(Request $request, $id): Response
    {
        $qcm = $doctrine->getRepository(QCM::class)->find($id);
        $form = $this->createForm( QCMType::class, $qcm);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager= $doctrine->getManager();
            $entityManager->persist($qcm);
            $entityManager->flush();
            
            $this->addFlash(
                'notice',
                'Le QCM a été modifié'
            );

       
            return $this->redirectToRoute('module', ['id'=> $qcm->getModule()->getId() ]);
        }
        return $this->render('qcm/modifier_qcm.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
    
    /**
     * @Route("/formateur/module/qcm/ordre_plus_qcm/{id}", name="ordre_plus_qcm")
     */

    public function ordrePlusQcm($id, PersistenceManagerRegistry $doctrine): Response
    {

        $qcm = $doctrine->getRepository(QCM::class)->find($id);
        $ordre = $qcm->getOrdre();
        
        $module = $qcm->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf) {
            if($pdf->getOrdre() == ($ordre + 1)){
                $pdf->setOrdre($pdf->getOrdre() - 1);
                $qcm->setOrdre($ordre + 1);
                $entityManager->persist($pdf);
                $entityManager->persist($qcm);
            }
        }
        foreach($module->getVideos() as $video) {
            if($video->getOrdre() == ($ordre + 1)){
                $video->setOrdre($video->getOrdre() - 1);
                $qcm->setOrdre($ordre + 1);
                $entityManager->persist($video);
                $entityManager->persist($qcm);
            }
        }
        foreach($module->getQcms() as $qcm1) {
            if($qcm1->getOrdre() == ($ordre + 1)){
                $qcm1->setOrdre($qcm1->getOrdre() - 1);
                $qcm->setOrdre($ordre + 1);
                $entityManager->persist($qcm1);
                $entityManager->persist($qcm);
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
     * @Route("/formateur/module/qcm/ordre_moins_qcm/{id}", name="ordre_moins_qcm")
     */

    public function ordreMoinsQcm($id, PersistenceManagerRegistry $doctrine): Response
    {

        $qcm = $doctrine->getRepository(QCM::class)->find($id);
        $ordre = $qcm->getOrdre();
        
        $module = $qcm->getModule();

        $entityManager= $doctrine->getManager();
        foreach($module->getPdfs() as $pdf) {
            if($pdf->getOrdre() == ($ordre - 1)){
                $pdf->setOrdre($pdf->getOrdre() + 1);
                $qcm->setOrdre($ordre - 1);
                $entityManager->persist($pdf);
                $entityManager->persist($qcm);
            }
        }
        foreach($module->getVideos() as $video) {
            if($video->getOrdre() == ($ordre - 1)){
                $video->setOrdre($video->getOrdre() + 1);
                $qcm->setOrdre($ordre - 1);
                $entityManager->persist($video);
                $entityManager->persist($qcm);
            }
        }
        foreach($module->getQcms() as $qcm1) {
            if($qcm1->getOrdre() == ($ordre - 1)){
                $qcm1->setOrdre($qcm1->getOrdre() + 1);
                $qcm->setOrdre($ordre - 1);
                $entityManager->persist($qcm1);
                $entityManager->persist($qcm);
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



