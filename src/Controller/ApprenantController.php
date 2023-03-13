<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Module;
use App\Entity\ModuleQuestion;
use App\Entity\ModuleReponse;
use App\Entity\Apprenant;
use App\Entity\PDF;
use App\Entity\Video;
use App\Entity\QCM;
use App\Entity\QCMReponse;
use App\Entity\QCMQuestion;
use App\Form\ModuleQuestionType;
use App\Form\ApprenantQCMReponseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
 
 
class ApprenantController extends AbstractController
 
 
{
   
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/liste_module_apprenant", name="liste_module_apprenant")
     */
    public function listeModuleApprenant(PersistenceManagerRegistry $doctrine): Response
    {
        $utilisateur = $this->getUser();
        $modules = $doctrine->getRepository(Module::class)->findAll();
 
        return $this->render('apprenant/liste_module_apprenant.html.twig', [
            'modules' => $modules,
            'utilisateur' => $utilisateur,
        ]);
    }
 
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/module/{id}", name="module_apprenant")
     */
    public function moduleApprenant(int $id, PersistenceManagerRegistry $doctrine): Response
    {
        $apprenant = $this->getUser()->getApprenant();
        $module = $doctrine->getRepository(Module::class)->find($id);
 
        foreach($apprenant->getModules() as $moduleApprenant) {
            if($module->getId() == $moduleApprenant->getId()){
                return $this->render('apprenant/module_apprenant.html.twig', [
                    'module' => $module,
                    'apprenant' => $apprenant,
            ]);
            }
        }
        throw new \Exception("une erreur s'est produite");
       
       
    }
 
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/module/{id}/video/{idFile}", name="cours_video_apprenant")
     */
    public function coursVideoApprenant(Request $request, $id, $idFile, PersistenceManagerRegistry $doctrine): Response
    {
        $apprenant = $this->getUser()->getApprenant();
        $module = $doctrine->getRepository(Module::class)->find($id);
        $video = $doctrine->getRepository(Video::class)->find($idFile);
        $moduleQuestion = new ModuleQuestion();
        $form = $this->createForm(ModuleQuestionType::class, $moduleQuestion);
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()) {
            $moduleQuestion->setVideo($video);
            $moduleQuestion->setApprenant($apprenant);
            $entityManager= $doctrine->getManager();
            $entityManager->persist($moduleQuestion);
            $entityManager->flush();
 
                    $this->addFlash( /*fonction message d'alerte après avoir ajouté*/
                        'notice', /*variable utilisé pour le twig à rappeler*/
                        'Super la question a été ajouté avec succès!'
                    );
   
            return $this->redirectToRoute('cours_video_apprenant', ['id' => $module->getId(), 'idFile' => $video->getId()]);
        }
        foreach($apprenant->getModules() as $moduleApprenant) {
            if($module->getId() == $moduleApprenant->getId()){
                return $this->render('apprenant/module_video_apprenant.html.twig', [
                    'module' => $module,
                    'apprenant' => $apprenant,
                    'video' => $video,
                    'form'=> $form->createView(),
 
            ]);
            }
        }
        throw new \Exception("une erreur s'est produite");
       
       
    }
 
   
 
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/module/{id}/pdf/{idFile}", name="cours_pdf_apprenant")
     */
    public function coursPdfApprenant(Request $request, $id, $idFile, PersistenceManagerRegistry $doctrine): Response
    {
        $apprenant = $this->getUser()->getApprenant();
        $module = $doctrine->getRepository(Module::class)->find($id);
        $pdf = $doctrine->getRepository(PDF::class)->find($idFile);
        $moduleQuestion = new ModuleQuestion();
        $form = $this->createForm(ModuleQuestionType::class, $moduleQuestion);
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()) {
            $moduleQuestion->setPdf($pdf);
            $moduleQuestion->setApprenant($apprenant);
            $entityManager= $doctrine->getManager();
            $entityManager->persist($moduleQuestion);
            $entityManager->flush();
 
                    $this->addFlash( /*fonction message d'alerte après avoir ajouté*/
                        'notice', /*variable utilisé pour le twig à rappeler*/
                        'Super la question a été ajouté avec succès!'
                    );
   
            return $this->redirectToRoute('cours_pdf_apprenant', ['id' => $module->getId(), 'idFile' => $pdf->getId()]);
        }
 
        foreach($apprenant->getModules() as $moduleApprenant) {
            if($module->getId() == $moduleApprenant->getId()){
                return $this->render('apprenant/module_pdf_apprenant.html.twig', [
                    'module' => $module,
                    'apprenant' => $apprenant,
                    'pdf' => $pdf,
                    'form'=> $form->createView(),
            ]);
            }
        }
        throw new \Exception("une erreur s'est produite");
       
       
    }
 
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/module/{id}/qcm/{idFile}", name="cours_qcm_apprenant")
     */
    public function coursQcmApprenant(Request $request,$id, $idFile, PersistenceManagerRegistry $doctrine): Response
    {
        $apprenant = $this->getUser()->getApprenant();
        $module = $doctrine->getRepository(Module::class)->find($id);
        $qcm = $doctrine->getRepository(QCM::class)->find($idFile);
        $qcmQuestions = $qcm->getQcmQuestions();
        $qcmReponses =[];
        foreach($qcmQuestions as $qcmQuestion){
            foreach($qcmQuestion->getQcmReponses() as $reponse){
                $qcmReponses[] = $reponse;
            }
        }
        $form = $this->createFormBuilder($apprenant)
            ->add('qcmReponses', EntityType::class, [
                'class' => QCMReponse::class,
                'multiple' => true,
                'expanded' => true,
                'choices' => $qcmReponses,
                ])  
            ->add('Envoyer', SubmitType::class,['attr' => ['class' => 'btn btn-secondary']])    
            ->getForm()
            ->handleRequest($request);    
 
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager= $doctrine->getManager();
            $entityManager->persist($apprenant);
            $entityManager->flush();
   
            return $this->redirectToRoute('resultat_qcm_apprenant', ['id' => $module->getId(), 'idFile' => $qcm->getId()]);
        }
 
        foreach($apprenant->getModules() as $moduleApprenant) {
            if($module->getId() == $moduleApprenant->getId()){
                return $this->render('apprenant/module_qcm_apprenant.html.twig', [
                    'module' => $module,
                    'apprenant' => $apprenant,
                    'qcm' => $qcm,
                    'qcmQuestions' => $qcmQuestions,
                    'qcmReponses' => $qcmReponses,
                    'form'=> $form->createView(),
            ]);
            }
        }
        throw new \Exception("une erreur s'est produite");
       
       
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/apprenant/module/{id}/qcm/{idFile}/resultat_qcm", name="resultat_qcm_apprenant")
     */
    public function resultatQcmApprenant($id, $idFile, PersistenceManagerRegistry $doctrine): Response
    {
        $apprenant = $this->getUser()->getApprenant();
        $module = $doctrine->getRepository(Module::class)->find($id);
        $qcm = $doctrine->getRepository(QCM::class)->find($idFile);
        $qcmQuestions = $qcm->getQcmQuestions();
        $qcmReponses =[];
        foreach($qcmQuestions as $qcmQuestion){
            foreach($qcmQuestion->getQcmReponses() as $reponse){
                $qcmReponses[] = $reponse;
            }
        }
        
        foreach($apprenant->getModules() as $moduleApprenant) {
            if($module->getId() == $moduleApprenant->getId()){
                return $this->render('apprenant/resultat_qcm_apprenant.html.twig', [
                    'module' => $module,
                    'apprenant' => $apprenant,
                    'qcm' => $qcm,
                    'qcmQuestions' => $qcmQuestions,
                    'qcmReponses' => $qcmReponses,
            ]);
            }
        }
        throw new \Exception("une erreur s'est produite");
       
       
    }
 
}