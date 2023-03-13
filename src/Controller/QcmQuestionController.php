<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\QCM;
use App\Entity\QCMQuestion;
use App\Form\QCMQuestionType;
use App\Form\QCMType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;




class QcmQuestionController extends AbstractController
{
    /**
     * @Route("formateur/qcm/question/{id}", name="ajout_question_qcm")
     */
    public function ajouterQuestionqcm(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $qcm = $doctrine->getRepository(QCM::class)->find($id);
        $qcmQuestion = new QCMQuestion();
        $form = $this -> createForm( QCMQuestionType::class, $qcmQuestion); // creation du form par rapport a nouvel objet
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager= $doctrine->getManager();
            $qcmQuestion->setQcm($qcm);
            $entityManager->persist($qcmQuestion);
            $entityManager->flush();

            return $this->redirectToRoute('qcm', ['id' => $id]);
        }
       
        return $this->render('qcm_question/ajout_question_qcm.html.twig', [
            'form'=>  $form->createView(),
            'qcm' => $qcm,

        ]);
    }

    /**
     * @Route("formateur/qcm/question/modifier/{id}", name="modifier_question_qcm")
     */
    public function modifierQuestionqcm(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $qcmQuestion = $doctrine->getRepository(QCMQuestion::class)->find($id);
        $form = $this -> createForm( QCMQuestionType::class, $qcmQuestion); // creation du form par rapport a nouvel objet
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager= $doctrine->getManager();
            $entityManager->persist($qcmQuestion);
            $entityManager->flush();

            return $this->redirectToRoute('qcm', ['id' => $qcmQuestion->getQcm()->getId()]);
        }
       
        return $this->render('qcm_question/modifier_question_qcm.html.twig', [
            'form'=>  $form->createView(),
            'question' => $qcmQuestion,
        ]);
    }

     /**
     * @Route("/formateur/qcm/question/supprimer/{id}", name="supprimer_question_qcm")
     */

    public function supprimerQuestionQcm($id): Response
    {

        $qcmQuestion = $doctrine->getRepository(QCMQuestion::class)->find($id);
        $entityManager= $doctrine->getManager();
        $entityManager->remove($qcmQuestion);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'supprimerQcmQuestion',
            'la question a été supprimée avec succès!'
        
        );

        return $this->redirectToRoute('qcm', ['id' => $qcmQuestion->getQcm()->getId()]);
     }
}
