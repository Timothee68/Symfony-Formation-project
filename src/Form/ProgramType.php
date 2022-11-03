<?php

namespace App\Form;

use App\Entity\Program;
use App\Entity\Session;
use App\Entity\Workshop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProgramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('session', HiddenType::class) pour cacher le champs
            ->add('workshop', EntityType::class, [
                'class' => Workshop::class,
                'choice_label' => 'title',
                "attr" => ['class' => "form-control "],
                'label' => 'Choisis le module a ajouter',
            ])
            ->add('nbDays', IntegerType::class, [
                "attr" => ['class' => "form-control " , 'min' => 1 ],
                'label' => 'Choisis le nombre de jour attitré au module pou la session ',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Program::class,
        ]);
    }
}
