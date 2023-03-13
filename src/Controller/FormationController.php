<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Formation;
use App\Form\FormationType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class FormationController extends AbstractController
{
    /**
     * @Route("/admin/liste_formations", name="admin_liste_formations")
     */
    public function listeFormations(PersistenceManagerRegistry $doctrine): Response
    {
        $formations = $doctrine->getRepository(Formation::class)->findAll();
        

        return $this->render('admin/listeFormations.html.twig', [
            'formations' => $formations,
        ]);
    }

    /**
     * @Route("/admin/ajouter_formation", name="ajouter_formation")
     */
    public function ajouterFormation(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            
            $entityManager= $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();
            $this->addFlash(
                'ajouterFormation',
                'La formation a été ajouté'
            );
            return $this->redirectToRoute('admin_liste_formations');
        }
        return $this->render('admin/ajouterFormation.html.twig', [
        'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/formations/supprimer/{id}", name="supprimer_formation")
     */
    public function supprimerFormation($id, PersistenceManagerRegistry $doctrine): Response
    {
        $formation = $doctrine->getRepository(Formation::class)->find($id);

        $em = $doctrine->getManager();
        $em->remove($formation);

        $em->flush();
        $this->addFlash(
            'supprimerFormation',
            'La formation a été supprimé'
        );

        return $this->redirectToRoute('admin_liste_formations');
    }

    /**
     * @Route("/admin/modifier_formation/{id}", name="modifier_formation")
     */
    public function modifierFormation(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {

        $formation = $doctrine->getRepository(Formation::class)->find($id);
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            
            $entityManager= $doctrine->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();
            $this->addFlash(
                'modifierFormation',
                'La formation a été modifier'
            );
            return $this->redirectToRoute('admin_liste_formations');
        }
        return $this->render('admin/modifierFormation.html.twig', [
        'form'=> $form->createView(),
        ]);
    }
}
