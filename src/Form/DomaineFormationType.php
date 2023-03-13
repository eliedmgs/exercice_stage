<?php

namespace App\Form;

use App\Entity\DomaineFormation;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class DomaineFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('image', FileType::class, [
                'label' => 'Image (png, jpeg, jpg) : ',
                'required' => false,
                'data_class' => null
            ])
            ->add('Enregistrer', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomaineFormation::class,
        ]);
    }
}
