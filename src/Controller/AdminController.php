<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\Repository\UtilisateurRepository;
Use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use App\Form\RegistrationFormType;
use App\Form\AjouterModuleType;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class AdminController extends AbstractController
{
    
    /**
     * @Route("/admin/", name="indexAdmin")
     */
    public function listeUtilisateurs(PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateurs = $doctrine->getRepository(Utilisateur::class)->findAll(); 
        

        return $this->render('admin/indexAdmin.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    /**
     * @Route("/admin/profilUtilisateur/{id}", name="profil_utilisateur_admin")
     */

    public function listeUtilisateur($id, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);

        return $this->render('admin/profilUtilisateurAdmin.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }



    /**
     * @Route("/admin/ajouter_utilisateur", name="ajouter_utilisateur")
     */
    public function ajouterUtilisateur(Request $request, UtilisateurRepository $UtilisateurRepository, UserPasswordHasherInterface $passwordHasher, PersistenceManagerRegistry $doctrine): Response 
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $apprenant = new Apprenant();
            $utilisateur->setPassword(
                $passwordHasher->hashPassword(
                $utilisateur,
                $form->get('plainPassword')->getData()
                )
            );
            $apprenant->setUtilisateur($utilisateur); /* l'utilisateur sera créer avec un ROLE_USER en tant qu' Apprenant */
            $utilisateur->setRoles(['ROLE_USER']);
            $entityManager= $doctrine->getManager();
            $entityManager->persist($apprenant);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'L\'utilisateur a été ajouté'
            );

            return $this->redirectToRoute('indexAdmin');
        }
        return $this->render('admin/ajouterUtilisateur.html.twig', [
        'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/utilisateurs/supprimer/{id}", name="supprimer_utilisateur")
     */
    public function supprimerUtilisateur($id, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);

        $em = $doctrine->getManager();
        $em->remove($utilisateur);

        $em->flush();
        $this->addFlash(
            'notice',
            'L\'utilisateur a été supprimé'
        );

        return $this->redirectToRoute('indexAdmin');
    }

    /**
     * @Route("/admin/utilisateurs/modifier/{id}", name="modifier_utilisateur")
     */
    public function modifierUtilisateur(Request $request, $id, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);
        $form = $this->createForm(RegistrationFormType::class, $utilisateur);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            
            $utilisateur->setPassword(
                $passwordHasher->hashPassword(
                    $utilisateur,
                    $form->get('plainPassword')->getData()
                    )
            );
            $entityManager= $doctrine->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'L\'utilisateur a été modifié'
            );
       
            return $this->redirectToRoute('indexAdmin');
        }
        return $this->render('admin/modifierUtilisateur.html.twig', [
        'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/utilisateurs/role_admin/{id}", name="role_admin")
     */
    public function roleAdmin($id, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);

        if (!in_array('ROLE_ADMIN', $utilisateur->getRoles())) {  /* On vérifie si le ROLE_ADMIN n'est pas dans le tableau des roles utilisateurs  */
            $roles = $utilisateur->getRoles();
            $tab = ['ROLE_ADMIN'];
            foreach($roles as $role) { /* On parcourt tous les roles du tableau  */
                $tab[] = $role;
            }
            $utilisateur->setRoles($tab); /* On set le ROLE_ADMIN dans le tableau */
            $em = $doctrine->getManager();
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash(
                'notice',
                'Le rôle admin a été ajouté'
            );
        }
        else {
            $tab = $utilisateur->getRoles();
            $element = 'ROLE_ADMIN';
            unset($tab[array_search($element, $tab)]); /* On recherche le ROLE_ADMIN dans le tableau des roles et on le retire si il est présent */
            $utilisateur->setRoles($tab);
            $em = $doctrine->getManager();
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash(
                'notice',
                'Le rôle admin a été retiré'
            );
        }
        
        

        return $this->redirectToRoute('indexAdmin');
    }

    /**
     * @Route("/admin/utilisateurs/role_formateur/{id}", name="role_formateur")
     */
    public function roleFormateur($id, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);
        
        if (!in_array('ROLE_FORMATEUR', $utilisateur->getRoles())) {  /* On vérifie si le ROLE_FORMATEUR n'est pas dans le tableau des roles utilisateurs  */
            $roles = $utilisateur->getRoles();
            $tab = ['ROLE_FORMATEUR'];
            foreach($roles as $role) {  /* On parcourt tous les roles du tableau  */
                $tab[] = $role;
            }
            $formateur = new Formateur();
            $utilisateur->setFormateur($formateur); /* On crée l'utilisateur en tant que nouveau Formateur */
            $utilisateur->setRoles($tab);   /* On set le ROLE_FORMATEUR dans le tableau */
            $em = $doctrine->getManager();
            $em->persist($formateur);
                $em->flush();
            $this->addFlash(
                'notice',
                'Le rôle formateur a été ajouté'
            );
        }
        else {
            $formateur = $utilisateur->getFormateur();
            $tab = $utilisateur->getRoles();
            $element = 'ROLE_FORMATEUR';
            unset($tab[array_search($element, $tab)]); /* On recherche le ROLE_FORMATEUR dans le tableau des roles et on le retire si il est présent */
            $utilisateur->setRoles($tab);
            $em = $doctrine->getManager();
            $em->persist($utilisateur);
            $em->remove($formateur); /* on retire le formateur de la base de données */
            $em->flush();
            $this->addFlash(
                'notice',
                'Le rôle formateur a été retiré'
            );
        }

        return $this->redirectToRoute('indexAdmin');
    }

    /**
     * @Route("/admin/utilisateurs/role_user/{id}", name="role_user")
     */
    public function roleUser($id, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);
        
        if (!in_array('ROLE_USER', $utilisateur->getRoles())) { /* On vérifie si le ROLE_USER n'est pas dans le tableau des roles utilisateurs  */
            $roles = $utilisateur->getRoles();
            $tab = ['ROLE_USER'];
            foreach($roles as $role) { /* On parcourt tous les roles du tableau  */
                $tab[] = $role;
            }
            $apprenant = new Apprenant();
            $utilisateur->setApprenant($apprenant); /* On crée l'utilisateur en tant que nouvel Apprenant */
            $utilisateur->setRoles($tab); /* On set le ROLE_USER dans le tableau */
            $em = $doctrine->getManager();
            $em->persist($apprenant);
            $em->flush();
            $this->addFlash(
            'notice',
            'Le rôle apprenant a été ajouté'
            );
        }
        else {
            $apprenant = $utilisateur->getApprenant();
            $tab = $utilisateur->getRoles();
            $element = 'ROLE_USER';
            unset($tab[array_search($element, $tab)]); /* On recherche le ROLE_USER dans le tableau des roles et on le retire si il est présent */
            $utilisateur->setRoles($tab);
            $em = $doctrine->getManager();
            $em->persist($utilisateur);
            $em->remove($apprenant);    /* on retire l'apprenant de la base de données */
            $em->flush();
            $this->addFlash(
                'notice',
                'Le rôle apprenant a été retiré'
            );
        }
        return $this->redirectToRoute('indexAdmin');
    }

    /**
     * @Route("/admin/profilUtilisateur/ajouter_modules/{id}", name="ajouter_modules_apprenant")
     */
    public function ajouterModule(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {

        $utilisateur = $doctrine->getRepository(Utilisateur::class)->find($id);
        $apprenant = $utilisateur->getApprenant();
        $form = $this->createForm(AjouterModuleType::class, $apprenant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            
            $entityManager= $doctrine->getManager();
            $entityManager->persist($apprenant);
            $entityManager->flush();
            $this->addFlash(
                'notice',
                'Les modules ont été ajoutés'
            );
       
            return $this->redirectToRoute('profil_utilisateur_admin', ['id' => $id]);
        }
        return $this->render('admin/ajouterModules.html.twig', [
        'form'=> $form->createView(),
        ]);
    }
}

