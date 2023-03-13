<?php

namespace App\Controller;

use App\Entity\Apprenant;
use App\Entity\Module;
use App\Entity\ModuleQuestion;
use App\Form\ModuleQuestionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;




class ModuleController extends AbstractController
{
    /**
     * @Route("/modules", name="liste_des_modules")
     */
    public function listeModules(PersistenceManagerRegistry $doctrine): Response
    {
        /** @var ModuleRepository $repository Repository */
        $repository = $doctrine->getRepository(Module::class);

        /** @var Module[] $modules Modules */
        $modules = $repository->findAll();



        return $this->render('module/liste_des_modules.html.twig', [
            'modules' => $modules,
        ]);
    }

    /**
     * @Route("/modules/modules_description/{id}", name="module_description")
     */

    public function moduleDescription(int $id, PersistenceManagerRegistry $doctrine ): Response
    {

        $repository = $doctrine->getRepository(Module::class);

        $module = $repository->find($id);

        return $this->render('module/module_description.html.twig', [
            'module' => $module,
        ]);
    }
     
}
