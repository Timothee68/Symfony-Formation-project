<?php

namespace App\Form;

use App\Entity\Intern;
use App\Entity\Session;
use App\Form\ProgramType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("title" , TextType::class, [
                "attr" => ['class' => "form-control"]
                ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"]
                ])

            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                "attr" => ['class' => "form-control"]
                ])

            ->add('nbPlace', IntegerType::class, [
                "attr" => ['class' => "form-control"]
                ])

            // ->add('interns', EntityType::class, [
            //     'choice_label' => 'name',
            //     'class' => Intern::class,
            //     'expanded' =>true,
            //     'multiple' => true,
            //     'query_builder' => function (EntityRepository $er) {
            //         return $er->createQueryBuilder('i')
            //             ->orderBy('i.name' , 'ASC');
            //     },
            //     "attr" => ['class' => "form-control "],
            //     'label' => 'Choisis des stagiaire a ajouté a la session de formation ',
            // ])
            ->add('programs' ,CollectionType::class, [
                    // la collection attend l'élément qu'elle entrera dans le form ce n'est pas obligatoire que se soit un autre form
                    'entry_type' => ProgramType::class,
                    'prototype' => true,
                    //  on va autoriser l'ajout  d'un nouvelle élément dans l'entité session qui seront persister grace au cascade persiste sur l'élément programme
                    // ca va activer un data prototype qui sera un attribu html qu'on pourra manipuler en js
                    'allow_add' => true, 
                    'allow_delete' => true,
                    // il obligatoire car Session  n'a pas de setProgramm() mais c'est Programme qui contient setSession() 
                    // Programme est propriaitaire de la relation, pour éviter un mapped a false on ajoute le by reference a false.
                    'by_reference' => false,
                    'entry_options' => [
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Choisis les modules qui composeront la session',
                ],
            ])
            ->add('submit',SubmitType::class, [
                "attr" => ['class' => "form-control bg-primary"]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
