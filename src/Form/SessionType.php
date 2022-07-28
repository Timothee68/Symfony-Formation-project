<?php

namespace App\Form;

use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
