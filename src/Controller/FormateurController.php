<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Module;
use App\Entity\ModuleQuestion;
use App\Entity\ModuleReponse;
use App\Form\ModuleQuestionType;
use App\Form\ModuleReponseType;
use App\Form\ModuleType;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;




/**
 * @IsGranted("ROLE_FORMATEUR")
 */

class FormateurController extends AbstractController
{
    /**
     * @Route("/formateur/liste_modules", name="liste_modules_formateur")
     */
    public function listeModulesFormateur(): Response
    {
        $modules = $this->getUser()->getFormateur()->getModules();
        return $this->render('formateur/liste_modules_formateur.html.twig', [
            'modules' => $modules,
        ]);
    }

    /************************************************************MODULE***************************************************************************************** */

    /**
     * @Route("/formateur/module/{id}", name="module")
     */

    public function moduleFormateur($id, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);

        return $this->render('formateur/module.html.twig', [
            'module' => $module,
        ]);
    }

     /************************************************************AJOUT MODULE************************************************************************* */

    /**
     * @Route("/formateur/ajout_module", name="ajout_module") 
     */
    
    public function ajouterModule(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $module = new Module(); 
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $imageFile= $form->get('image')->getData();

            if($imageFile != null){
                $module->setExtension($imageFile->guessExtension());
            };
            $formateur = $this->getUser()->getFormateur();
            
            $module->setFormateur($formateur);
            $entityManager= $doctrine->getManager();
            $entityManager->persist($module);
            $entityManager->flush(); 


            if($imageFile != null){
                try {
                    $imageFile->move(
                        $this->getParameter('dossier_module'),
                        "imgModule".$module->getId().'.'.$imageFile->guessExtension()
                    );

                } catch (FileException $e) {
                    $e->error("Le fichier n'est pas valide");
                }
            }

                    $this->addFlash( 
                        'notice', 
                        'Super le module a été ajouté avec succès!'
                    );
    
            return $this->redirectToRoute('liste_modules_formateur');
        }
 
        return $this->render('formateur/ajout_module.html.twig', [ 
            'form'=> $form->createView(),

    ]);
    }       

    /************************************************************MODULE: SUPPRIMER************************************************************************* */

    /**
     * @Route("/formateur/module/supprimer/{id}", name="supprimer_module")
     */

    public function supprimerModule($id, PersistenceManagerRegistry $doctrine): Response
    {

        $module = $doctrine->getRepository(Module::class)->find($id);
        $image = $module->getImage();
        $ancienneExtension = $module->getExtension();
        if($image != null){
            $filesystem = new Filesystem();
            //ON VERIFIE SI UN POSTER A DEJA ETE UPLOADER
            if ($filesystem->exists($this->getParameter('dossier_module'), "imgModule".$module->getId().'.'.$ancienneExtension)){
                //Si L'AFFICHE EXISTE ON LA SUPPRIME
                $filesystem->remove($this->getParameter('dossier_module')."/imgModule".$module->getId().'.'.$ancienneExtension);
            }
        }
        $entityManager= $doctrine->getManager();
        $entityManager->remove($module);/*la fonction « remove » pour « marquer » une entité afin qu’elle soit supprimée */
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Super le module a été supprimé avec succès!'
        
        );

        return $this->redirectToRoute('liste_modules_formateur');/*si valide vers cette route*/
     }
    
     /************************************************************MODULE: MODIFIER************************************************************************* */

    /**
     * @Route("/formateur/module/modifier/{id}", name="modifier_module")
     */
    public function modifierModule(Request $request, $id, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $image= $module->getImage();

            if($image != null){
                $ancienneExtension = $module->getExtension();
                $module->setExtension($image->guessExtension());
            };

            $entityManager= $doctrine->getManager();
            $entityManager->persist($module);
            $entityManager->flush();

            if($image != null){
                $filesystem = new Filesystem();
                //ON VERIFI SI UN POSTER A DEJA ETE UPLOADER
                if ($filesystem->exists($this->getParameter('dossier_module'), "imgModule".$module->getId().'.'.$ancienneExtension)){
                    //Si L'AFFICHE EXISTE ON LA SUPPRIME
                    $filesystem->remove($this->getParameter('dossier_module')."/imgModule".$module->getId().'.'.$ancienneExtension);
                }
                try {
                    $image->move(
                        $this->getParameter('dossier_module'),
                        "imgModule".$module->getId().'.'.$image->guessExtension()
                    );

                } catch (FileException $e) {
                    $e->error("Le fichier n'est pas valide");
                }
            }

            $this->addFlash(
                'notice',
                'Le module a été modifié'
            );

         
       
            return $this->redirectToRoute('liste_modules_formateur');
        }
        return $this->render('formateur/modifier_module.html.twig', [
        'form'=> $form->createView(),
        ]);
    }

    

    
    /************************************************************MODULE REPONSE************************************************************************* */

    /**
     * @Route("/formateur/module/liste_module_questions_reponses/{id}", name="liste_module_questions_reponses")
     */

    public function listeModuleQuestionsReponsesFormateur($id, PersistenceManagerRegistry $doctrine): Response
    {
        $module = $doctrine->getRepository(Module::class)->find($id);

        return $this->render('formateur/liste_module_questions_reponses.html.twig', [
            'module' => $module,
        ]);
    }

    /**
     * @Route("/formateur/module/{id}/ajouter_module_reponse/{idQuestion}", name="ajouter_module_reponse")
     */
    
    public function ajouterModuleReponses(Request $request, $id, $idQuestion, PersistenceManagerRegistry $doctrine): Response
    {
        $formateur = $this->getUser()->getFormateur();    
        $module = $doctrine->getRepository(Module::class)->find($id); 
        $moduleQuestion = $doctrine->getRepository(ModuleQuestion::class)->find($idQuestion);
        $moduleReponse = new ModuleReponse(); 
        $form = $this->createForm(ModuleReponseType::class, $moduleReponse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $moduleReponse->setModuleQuestion($moduleQuestion);
            $moduleReponse->setFormateur($formateur);
            $entityManager= $doctrine->getManager();
            $entityManager->persist($moduleReponse);
            $entityManager->flush();

                    $this->addFlash( 
                        'notice', 
                        'Super la réponse a été ajoutée avec succès!'
                    );
    
            return $this->redirectToRoute('liste_module_questions_reponses', ['id' => $module->getId()]);
        }
    

        return $this->render('formateur/ajout_module_reponse.html.twig', [ 

                    'form'=> $form->createView(),
                    'module' => $module,
                    'moduleReponse' => $moduleReponse,
                    'moduleQuestion' => $moduleQuestion,         
    ]);

    }

    /**
     * @Route("/formateur/module/{id}/modifier_module_reponse/{idReponse}/", name="modifier_module_reponse")
     */
    
    public function modifierModuleReponses(Request $request, $id, $idReponse, PersistenceManagerRegistry $doctrine): Response
    {
        $formateur = $this->getUser()->getFormateur();    
        $module = $doctrine->getRepository(Module::class)->find($id); 
        $moduleReponse = $doctrine->getRepository(ModuleReponse::class)->find($idReponse);
        $form = $this->createForm(ModuleReponseType::class, $moduleReponse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager= $doctrine->getManager();
            $entityManager->persist($moduleReponse);
            $entityManager->flush();

                    $this->addFlash( 
                        'notice', 
                        'Super la réponse a été modifiée avec succès!'
                    );
    
            return $this->redirectToRoute('liste_module_questions_reponses', ['id' => $module->getId()]);
        }
    

        return $this->render('formateur/modifier_module_reponse.html.twig', [ 

                    'form'=> $form->createView(),
                    'module' => $module,
                    'moduleReponse' => $moduleReponse,
        
    ]);

    }

    /**
     * @Route("/formateur/module/{id}/supprimer_module_reponse/{idReponse}/", name="supprimer_module_reponse")
     */
    
    public function supprimerModuleReponses(Request $request, $id, $idReponse): Response
    {
        $formateur = $this->getUser()->getFormateur();    
        $module = $doctrine->getRepository(Module::class)->find($id); 
        $moduleReponse = $doctrine->getRepository(ModuleReponse::class)->find($idReponse);


            $entityManager= $doctrine->getManager();
            $entityManager->remove($moduleReponse);
            $entityManager->flush();

                    $this->addFlash( 
                        'notice', 
                        'Super la réponse a été supprimée avec succès!'
                    );
    
            return $this->redirectToRoute('liste_module_questions_reponses', ['id' => $module->getId()]);
    }
    

    
}   

