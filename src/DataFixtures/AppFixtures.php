<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $superAdmin = new Utilisateur();
        $superAdmin->setEmail('superadmin@gmail.com');
        $superAdmin->setNom('SuperAdmin');
        $superAdmin->setPrenom('SuperAdmin');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN','ROLE_FORMATEUR', 'ROLE_USER']);
        $superAdmin->setPassword('$2y$13$Hr7JvcYm.eeSJTHL4OhWxOFSOGrjJsHTsUDN23BnM.yyKINxGXlCW'); // (superadmin)
        $formateur = new Formateur();
        $formateur->setUtilisateur($superAdmin);
        $apprenant = new Apprenant();
        $apprenant->setUtilisateur($superAdmin);
        $manager->persist($formateur);
        $manager->persist($apprenant);

        $admin = new Utilisateur();
        $admin->setEmail('admin@gmail.com');
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$3i7GFdqAhiP9XThN/YIbO.XJbWkqDSU0SPj.BSUTsz7oBuoXpNQiG'); // (admin)
        $manager->persist($admin);

        $formateurUtilisateur = new Utilisateur();
        $formateurUtilisateur->setEmail('formateur@gmail.com');
        $formateurUtilisateur->setNom('formateur');
        $formateurUtilisateur->setPrenom('formateur');
        $formateurUtilisateur->setRoles(['ROLE_FORMATEUR']);
        $formateurUtilisateur->setPassword('$2y$13$TGjlkAQz3CKpChPwWgpj6eioyFtMaUFvTB9lXRuGKF1S2EasqBw7W'); // (formateur)
        $formateur = new Formateur();
        $formateur->setUtilisateur($formateurUtilisateur);
        $manager->persist($formateur);

        for($i=0 ; $i<25; $i++){
            $utilisateurTest = new Utilisateur();
            $utilisateurTest->setEmail('user'.$i.'@gmail.com');
            $utilisateurTest->setNom('user');
            $utilisateurTest->setPrenom('user');
            $utilisateurTest->setRoles(['ROLE_USER']);
            $utilisateurTest->setPassword('$2y$13$gKl2ltMxyBr/G7RLnQNtJu2s9o/pV3E.E2loasv5ddZnYfjSqz2AK'); // (utilisateur)
            $apprenant = new Apprenant();
            $apprenant->setUtilisateur($utilisateurTest);
            $manager->persist($apprenant);
        }
           
        $manager->flush();
    }
}
