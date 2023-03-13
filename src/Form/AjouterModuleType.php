<?php

namespace App\Form;

use App\Entity\Apprenant;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class AjouterModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modules', EntityType::class, [
                'class' => Module::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('g')->orderBy('g.nom', 'Asc');
                },
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                ])
            ->add('Enregistrer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apprenant::class,
        ]);
    }
}
