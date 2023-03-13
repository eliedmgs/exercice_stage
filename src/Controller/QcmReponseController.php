<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\QCM;
use App\Entity\QCMQuestion;
use App\Entity\QCMReponse;
use App\Form\QCMQuestionType;
use App\Form\QCMType;
use App\Form\QCMReponseType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class QcmReponseController extends AbstractController
{
      /**
     * @Route("formateur/qcm/reponse/{id}", name="ajout_reponse_qcm")
     */
    public function ajouterReponseqcm(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $qcmQuestion = $doctrine->getRepository(QCMQuestion::class)->find($id);
        $qcmReponse = new QCMReponse();
        $form = $this -> createForm( QCMReponseType::class, $qcmReponse); // creation du form par rapport a nouvel objet
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager= $doctrine->getManager();
            $qcmReponse ->setQcmQuestion($qcmQuestion);
            $entityManager->persist($qcmReponse);
            $entityManager->flush(); 

            return $this->redirectToRoute('qcm', ['id' => $qcmQuestion->getQcm()->getId()]);
        }
       
        return $this->render('qcm_reponse/ajout_reponse_qcm.html.twig', [
            'form'=>  $form->createView(),
        ]);
    }
    
    /**
     * @Route("formateur/qcm/reponse/modifier/{id}", name="modifier_reponse_qcm")
     */
    public function modifierReponseQcm(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $qcmReponse = $doctrine->getRepository(QCMReponse::class)->find($id);
        $form = $this -> createForm( QCMReponseType::class, $qcmReponse); // creation du form par rapport a nouvel objet
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager= $doctrine->getManager();
            $entityManager->persist($qcmReponse);
            $entityManager->flush();

            return $this->redirectToRoute('qcm', ['id' => $qcmReponse->getQcmQuestion()->getQcm()->getId()]);
        }
       
        return $this->render('qcm_reponse/modifier_reponse_qcm.html.twig', [
            'form'=>  $form->createView(),
            'reponse' => $qcmReponse,
        ]);
    }

     /**
     * @Route("/formateur/qcm/reponse/supprimer/{id}", name="supprimer_reponse_qcm")
     */

    public function supprimerReponseQcm($id, PersistenceManagerRegistry $doctrine): Response
    {

        $qcmReponse = $doctrine->getRepository(QCMReponse::class)->find($id);
        $entityManager= $doctrine->getManager();
        $entityManager->remove($qcmReponse);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'supprimerQcmReponse',
            'la réponse a été supprimée avec succès!'
        
        );

        return $this->redirectToRoute('qcm', ['id' => $qcmReponse->getQcmQuestion()->getQcm()->getId()]);
     }
}
