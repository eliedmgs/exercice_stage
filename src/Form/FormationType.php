<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Module;
use App\Entity\DomaineFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('domaineFormation', EntityType::class, [
                'class' => DomaineFormation::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('Envoyer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
