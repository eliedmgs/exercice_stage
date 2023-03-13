<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\ModuleQuestion;
use App\Entity\ModuleReponse;
use App\Entity\Apprenant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ModuleQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question', TextareaType::class)
            ->add('Ajouter_une_question', SubmitType::class,['attr' => ['class' => 'btn btn-outline-secondary mt-3']]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModuleQuestion::class,
        ]);
    }
}
