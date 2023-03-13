<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Form\ModifierMotDePasseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;


class UtilisateurController extends AbstractController
{

    /**
     * @Route("/", name="accueil_principal")
     */
    public function index(): Response
    {
        return $this->render('indexPrincipal.html.twig', [
            'controller_name' => 'ApprenantController',
        ]);
    }

    
    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profil_utilisateur", name="profil_utilisateur")
     */
    public function getProfil(): Response
    {
        $utilisateur = $this->getUser();
        return $this->render('utilisateur/profilUtilisateur.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/profil_utilisateur/modifier_mot_de_passe", name="modifier_mot_de_passe_utilisateur")
     */
    public function modifierMotDePasse(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $this->getUser();
        $form = $this->createForm(modifierMotDePasseType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $request->request->get('modifier_mot_de_passe')['oldPassword'];
            $newPassword = $request->request->get('modifier_mot_de_passe')['newPassword']['first'];
            if($passwordEncoder->isPasswordValid($utilisateur, $oldPassword)){
                $this->addFlash(
                    'MotDePasseModifier',
                    'Le mot de passe a été modifié'
                );
                $password = $passwordEncoder->encodePassword($utilisateur, $newPassword);
                $utilisateur->setPassword($password);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($utilisateur);
                $entityManager->flush();
                return $this->redirectToRoute('profil_utilisateur');
            }
            else{
                $this->addFlash('erreur', 'Mot de passe incorrect');
            }
        }
        return $this->render('utilisateur/modifierMotDePasseUtilisateur.html.twig',array(
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ));
    }
}
