<?php

namespace App\Form;

use App\Entity\QCMQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QCMQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('question', TextType::class)
        ->add('Enregistrer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']])
            
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QCMQuestion::class,
        ]);
    }
}
