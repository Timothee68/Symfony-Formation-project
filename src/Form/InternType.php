<?php

namespace App\Form;

use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class InternType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('firstName', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"]
                ])
            ->add('civility', ChoiceType::class, [
                'choices' =>[
                    "Monsieur" => "Monsieur",
                    "Madame"=> "Madame",
                    "Autres"=>"Autres",
                ],
                "attr" => ['class' => "form-control"]
                ])
            ->add('email', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('cp', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('city', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('adress', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('telephone', TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intern::class,
        ]);
    }
}
