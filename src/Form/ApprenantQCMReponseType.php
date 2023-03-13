<?php

namespace App\Form;

use App\Entity\Apprenant;
use App\Entity\QCMReponse;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ApprenantQCMReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qcmReponses', EntityType::class, [
                'class' => QCMReponse::class,
                'choice_label' => 'reponse',
                'multiple' => true,
                'expanded' => true,
                ])
            ->add('Envoyer', SubmitType::class,['attr' => ['class' => 'btn btn-secondary']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Apprenant::class,
        ]);
    }
}
