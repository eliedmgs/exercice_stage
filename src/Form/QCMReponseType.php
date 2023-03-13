<?php

namespace App\Form;

use App\Entity\QCMReponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class QCMReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponse', TextType::class)
            ->add('bonneReponse', CheckboxType::class, ['required' => false])
            ->add('Enregistrer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QCMReponse::class,
        ]);
    }
}
