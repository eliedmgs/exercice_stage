<?php

namespace App\Form;

use App\Entity\PDF;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PDFType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
                       
            ->add('file', FileType::class ,[
                'label' => 'Ajoutez un pdf',
                'data_class'=> null,           
            ])

            ->add('Envoyer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PDF::class,
            
        ]);
    }
}
